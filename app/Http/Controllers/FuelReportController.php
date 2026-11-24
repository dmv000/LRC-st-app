<?php
namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;

use Excel;

use App\User as EMT;
use App\Responses\Response;
use App\Http\ORM\AppSettings;
use App\Http\ORM\FuelCoupon as FC;
use App\Http\ORM\fuel_stations as FuelStation;
use App\Http\ORM\fuel_types as FuelType;
use App\Http\ORM\ambulance as Ambulance;
use App\User;
use Illuminate\Support\Facades\Log;


class FuelReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generate_view() {
	if(Auth::check() && (
		Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Drivers Admin')->first()->id)
			||
		Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id)
		)) {

	$page_title = "Generate Fuel Report";
        return view('generate_fuel_report',compact('page_title'));
	}else{
		return view('errors.404');
	}
    }

    public function index()
    {

        $StartDate = Request::get('StartDate');
        $EndDate = Request::get('EndDate');
        $report = "";

        if($StartDate == ""){
            $StartDate = DateTime::createFromFormat('Y-m-d',Date("Y-m-d"))->modify('-' . AppSettings::where('name','fuel_report_go_back')->first()->value);
            $EndDate = DateTime::createFromFormat('Y-m-d',Date("Y-m-d"));
        }else{
            $StartDate = DateTime::createFromFormat('m/d/Y',$StartDate);//->modify('-' . AppSettings::where('name','fuel_report_go_back')->first()->value);
            $EndDate = DateTime::createFromFormat('m/d/Y',$EndDate);
        }

	$logs = FC::select(	'id',
				'date',
				'kms',
				'coupon_number',
				'litre',
				'price',
				'fuel_station_id',
				'fuel_type_id',
				'driver_id',
				'car_id')
			->whereBetween('date', array($StartDate->format('Y-m-d') , $EndDate->format('Y-m-d')))
//			->where('date','<=',$EndDate)
			->where('is_deleted',0)
			->orderBy('date','desc')
			->get();			

	foreach($logs as $log) {

		$log->driver = User::select('nickname')->where('id',$log->driver_id)->first()->nickname;
		$log->ambulance = Ambulance::select('car_number')->where('id',$log->car_id)->first()->car_number;
		$log->station = FuelStation::select('name')->where('id',$log->fuel_station_id)->first()->name;
		$log->type = FuelType::select('description')->where('id',$log->fuel_type_id)->first()->description;
	}
        $PageTitle = 'Driver Fuel Report / بونات البنزين للسائق';

        $EndDate = $EndDate->format('m/d/Y');
        $StartDate = $StartDate->format('m/d/Y');

        return view('fuel_report',compact('PageTitle','StartDate','EndDate','driver','logs'));
    }

	
    public function generateReport() {

	if(Auth::check() && ( Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Drivers Admin')->first()->id)
		||
		Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id))) {
	$s = json_decode(Request::get('form'));
	$cars = Ambulance::where('is_deleted',0)->get();

	foreach($cars as $car) {
		
		if(!$this->generate($car->id,$s->year,$s->month)) {
			$flag = false;
			$response = new Response;
			$response->Status = 0;
			$response->Message = "Consult IT Please";
			return "Error";
		}
	}

	$files = glob(storage_path('exports/' . date('F', mktime(0,0,0,(int)$s->month,10)) . "_" . date('Y', mktime(0,0,0,1,10,(int)$s->year)) . '*'));
	if(count($files) == 0)
		$files = glob(storage_path('exports/No_*'));
	$name = date('F', mktime(0,0,0,(int)$s->month,10)) . "_" . date('Y', mktime(0,0,0,1,10,(int)$s->year)) . '.zip';
	\Zipper::make(storage_path('exports/FuelReport_' . $name))->add($files)->close();
        return response()->download(storage_path('exports/FuelReport_' . $name));
	}else {
		return view('errors.404');
	}
	
    }

    private function generate($car,$year,$month){

	$data_raw = FC::whereYear('date', '=', $year)
              		->whereMonth('date', '=', $month)
			->where('car_id', $car)
			->where('is_deleted',0)
			->orderBy('date')
			->get();
	if(count($data_raw) == 0) { return true; }

        ini_set('memory_limit', '-1');
        Excel::create(date('F', mktime(0,0,0,(int)$month,10)) . "_" . date('Y', mktime(0,0,0,1,10,(int)$year)) . "_" . Ambulance::where('id',$car)->first()->car_number, function($excel) use($car,$year,$month,$data_raw) {

            // Set the title
            $excel->setTitle('Fuel Report');

            // Chain the setters
            $excel->setCreator('502')->setCompany('LRC');

	    $data = array();
	    foreach($data_raw as $index=>$d) {

		$row = array();
		$row[] = $index + 1;
		$row[] = $d->date;
		$row[] = $d->kms;
		$row[] = $d->coupon_number;
		$row[] = '';
		$row[] = '';
		if($d->fuel_type_id == 3) {
			$row[] = '';
			$row[] = $d->litre;
		} else {
			$row[] = $d->litre;
			$row[] = '';
		}
		$row[] = $d->price;
		$station = FuelStation::where('id',$d->fuel_station_id)->first();
		$row[] = $station->name;
		$row[] = $station->commercial_number;
		$data[] = $row;	
	    }

            $excel->sheet('Sheet 1', function ($sheet) use ($data,$car,$year,$month) {      
                
                $count=count($data);

                $sheet->fromArray($data, null, 'A6');

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  10,
                        'bold'      =>  false
                    )
                ));

                $str = 'A1:K' . ($count + 9);
                $sheet->getStyle($str)->getAlignment()->setWrapText(true); 

                $sheet->getStyle('A5')->getAlignment()->setTextRotation(90);
                $sheet->setRightToLeft(true);
                $sheet->setOrientation('landscape');

		$sheet->setCellValue('C3',Ambulance::where('id',$car)->first()->car_number);
		$sheet->setCellValue('E3',date('F', mktime(0,0,0,(int)$month,10)) . " " . date('Y', mktime(0,0,0,1,10,(int)$year)));
		$sheet->setCellValue('H3',"502");
		$sheet->setCellValue('K3',"500");

                $sheet->setCellValue('J5',"اسم المحطّة");
                $sheet->setCellValue('K5',"رقم تسجيل \nTVA");

                $sheet->setCellValue('G6',"بنزين \nليتر");
                $sheet->setCellValue('H6',"مازوت \nليتر");
                $sheet->setCellValue('I6',"المبلغ\nل.ل.");
                $sheet->setCellValue('B1','استهلاك سيّارات قسم الإسعاف من البنزين والمازوت');

                $sheet->setCellValue('B3',"سيارة رقم:");
                $sheet->setCellValue('D3'," الشهر:");
                $sheet->setCellValue('G3',"المنطقة:");
                $sheet->setCellValue('J3',"الاقليم:");

                $sheet->setCellValue('A5',"عدد البونات");
                $sheet->setCellValue('B5',"التاريخ");
                $sheet->setCellValue('C5',"قراءة العدّاد");
                $sheet->setCellValue('D5',"رقم الإيصال");
                $sheet->setCellValue('E5',"رقم قسيمة الجيش");
                $sheet->setCellValue('F5',"التسديد بموجب قسيمة الجيش عدد الليترات");
                $sheet->setCellValue('G5',"التسديد النقدي");

		$sheet->setColumnFormat(array(
		    'G' => '0.00',
		    'H' => '0.00',
		    'I' => '0,00',
		));

                $str = 'F' . ($count+7);
                $str2 = '=SUM(F7:F' . ($count+6) . ')';
                $sheet->setCellValue($str,$str2);

                $str = 'G' . ($count+7);
                $str2 = '=SUM(G7:G' . ($count+6) . ')';
                $sheet->setCellValue($str,$str2);

                $str = 'H' . ($count+7);
                $str2 = '=SUM(H7:H' . ($count+6) . ')';
                $sheet->setCellValue($str,$str2);

                $str = 'I' . ($count+7);
                $str2 = '=SUM(I7:I' . ($count+6) . ')';
                $sheet->setCellValue($str,$str2);

                $str = 'E' . ($count + 7);
                $sheet->setCellValue($str,"المجموع");
                $str = 'F' . ($count + 8);
                $sheet->setCellValue($str,"ليتر");
                $str = 'G' . ($count + 8);
                $sheet->setCellValue($str,"ليتر");
                $str = 'H' . ($count + 8);
                $sheet->setCellValue($str,"ليتر");
                $str = 'I' . ($count + 8);
                $sheet->setCellValue($str,"ل.ل.");
                //Shett Font Style

                $str = 'B' . ($count + 10);
                $sheet->setCellValue($str,"مسؤول سيارات المركز");
                $str = 'B' . ($count + 11);
                $sheet->setCellValue($str,"التوقيع:");
                $str = 'B' . ($count + 12);
                $sheet->setCellValue($str,"التاريخ:");

                $str = 'D' . ($count + 10);
                $sheet->setCellValue($str,"رئيس المركز");
                $str = 'D' . ($count + 11);
                $sheet->setCellValue($str,"التوقيع:");
                $str = 'D' . ($count + 12);
                $sheet->setCellValue($str,"التاريخ:");

                $str = 'F' . ($count + 10);
                $sheet->setCellValue($str,"رئيس الإقليم");
                $str = 'F' . ($count + 11);
                $sheet->setCellValue($str,"التوقيع:");
                $str = 'F' . ($count + 12);
                $sheet->setCellValue($str,"التاريخ:");

                $str = 'I' . ($count + 10);
                $sheet->setCellValue($str,"مكتب النقل والمواصلات");
                $str = 'I' . ($count + 11);
                $sheet->setCellValue($str,"التوقيع:");
                $str = 'I' . ($count + 12);
                $sheet->setCellValue($str,"التاريخ:");

                $str = 'K' . ($count + 10);
                $sheet->setCellValue($str,"مدير الإسعاف");
                $str = 'K' . ($count + 11);
                $sheet->setCellValue($str,"التوقيع:");
                $str = 'K' . ($count + 12);
                $sheet->setCellValue($str,"التاريخ:");


                

                //Column Width
                $sheet->setWidth('A', 2.57 + 0.71);
                $sheet->setWidth('B',11.14 + 0.71);
                $sheet->setWidth('C',11.14 + 0.71);
                $sheet->setWidth('D',9.71 + 0.71);
                $sheet->setWidth('E',10.71 + 0.71);
                $sheet->setWidth('F',11.86 + 0.71);
                $sheet->setWidth('G',6 + 0.71);
                $sheet->setWidth('H',6.71 + 0.71);
                $sheet->setWidth('I',12.71 + 0.71);
                $sheet->setWidth('J',17 + 0.71);
                $sheet->setWidth('K',17.71 + 0.71);

                //Row Height


                $sheet->mergeCells('B1:K1');
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6');
                $sheet->mergeCells('E5:E6');
                $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:I5');
                $sheet->mergeCells('J5:J6');
                $sheet->mergeCells('K5:K6');
                $str = 'E' . ($count + 7) . ':E' . ($count + 8);
                $sheet->mergeCells($str);





                $sheet->setHeight(1, 18.75 + 0.71);
                $sheet->setHeight(2, 12.75 + 0.71);
                $sheet->setHeight(3, 12.75 + 0.71);
                $sheet->setHeight(4, 13.5 + 0.71);
                $sheet->setHeight(5, 39.75 + 0.71);
                $sheet->setHeight(6, 25.5 + 0.71);
                for($i=0;$i<$count;$i++){
                    $sheet->setHeight(7+$i, 20 + 0.71);    
                }
            
                $sheet->setHeight($count + 7, 13.5 + 0.71);
                $sheet->setHeight($count + 8, 13.5 + 0.71);
                $sheet->setHeight($count + 9, 12.75 + 0.71);
                $sheet->setHeight($count + 10, 12.75 + 0.71);
                $sheet->setHeight($count + 11, 15 + 0.71);
                $sheet->setHeight($count + 12, 15 + 0.71);

                $sheet->getStyle("B1:K3")->getFont()->setBold(true);
                $str = 'B' . ($count + 10) . ':K' . ($count + 10);
                $sheet->getStyle($str)->getFont()->setBold(true);
                $sheet->getStyle("B1:K1")->getFont()->setSize(14);

                $str = 'A1:K' . ($count + 9);
                $sheet->cells($str, function($cells) {
                    $cells->setAlignment('center');
                    $cells->setvAlignment('center');
                });
                $sheet->cells('B3:J3', function($cells) {
                    $cells->setAlignment('right');
                });
                
                $sheet->cells('B1', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $sheet->cells('B3', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $sheet->cells('D3', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $sheet->cells('G3', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $sheet->cells('J3', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $sheet->cells('G5', function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $str = 'F' . ($count+7) . ':I' . ($count+7);
                $sheet->cells($str, function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $str = 'B' . ($count+10) . ':K' . ($count+10);
                $sheet->cells($str, function($cells) {
                    $cells->setBackground('#c0c0c0');
                });

                $str = 'B7:K' . ($count + 6);
                $sheet->setBorder($str, 'thin');

                $sheet->setBorder('B5:K6', 'medium');

                $str = 'F' . ($count + 7) . ':I' . ($count + 8);
                $sheet->setBorder($str, 'medium');

            });

        })->save('xlsx');
	return true;
    }
}
