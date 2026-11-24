<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;
use Illuminate\Support\Facades\Input;
use Excel;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use App\Http\ORM\EdjHdr;
use App\Http\ORM\EdjDtl;

use Illuminate\Support\Facades\Log;

class EdjController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index() {		

		try {

			$curr_date = date('Y-m-d');
			$month_val = strtotime('1 month');
			$next_month_y = date('Y', $month_val);
			$next_month_m = date('m', $month_val);

			$year = Input::get('year') == "" ? $next_month_y : Input::get('year');
			$month = Input::get('month') == "" ? $next_month_m : Input::get('month'); 

			$user_events = array();
			$user_shifts = EdjHdr::where('is_deleted',0)
						->where('user_id',Auth::user()->id)
						->where('year',$year)
						->where('month',$month)
						->first();

			if( !is_null ( $user_shifts ) ) {
				
				$events = EdjDtl::where('edj_hdr_id', $user_shifts->id)->get();
				foreach($events as $event) {

					$str = "event_id_" 
						. (trim(explode(" ",$event->type)[0]) === "Technical" ? "C". trim(explode(" ",$event->type)[1]) : trim(explode(" ",$event->type)[0]) )
						. "-" 
						. ltrim(explode("-",$event->date)[2],"0"); 
					$user_events[] = $str;
				}
			}
			$page_title = "Equipe de Jour";
			return view('pick_edj', compact( 'year' , 'month' , 'user_events' , 'page_title' ));

		} catch ( \Exception $e ) {

			Log::warning($e);
			$year = date('Y');
			$month = date('m');
			$user_events = array();
			$page_title = "Equipe de Jour.";

			return view('pick_edj', compact( 'year' , 'month' , 'user_events' , 'page_title' ));			
		}
	}

    public function store() {

	$s = json_decode(Request::get('form'));
	
	File::append(storage_path('logs/Edj_Log.txt'),"User : " . Auth::user()->nickname . "\n");
	File::append(storage_path('logs/Edj_Log.txt'),"At : " . date("Y-m-d h:i:sa") ."\n");
	File::append(storage_path('logs/Edj_Log.txt'),json_encode($s)."\n");

	try {
	
	    \DB::transaction(function() use ($s) {
		$response = new Response;
		
		//Check Old Data
		$old = EdjHdr::where('user_id',Auth::user()->id)
					->where('is_deleted',0)
					->where('year',$s->year)
					->where('month',$s->month)
					->first();
		if(!is_null($old)) {
			$old->is_deleted = 1;
			$old->save();
		}

		//Create new Header
		$new_hdr = new EdjHdr();
		$new_hdr->user_id = Auth::user()->id;
		$new_hdr->year = $s->year;
		$new_hdr->month = $s->month;
		$new_hdr->save();
		$new_hdr_id = $new_hdr->id;

		foreach($s->shifts as $shift) {

			$dtl = new EdjDtl();
			$dtl->edj_hdr_id = $new_hdr_id;
			$vals = explode("-", str_replace("event_id_","",$shift->value));
			$dtl->date = $s->year . "-" . $s->month . "-" . $vals[1];
			$dtl->type = $vals[0] == "AM" ? "AM Shift" : ( $vals[0] == "PM" ? "PM Shift" : ( $vals[0] == "CAM" ? "Technical AM Shift" : "Technical PM Shift" ));
			$dtl->save();
		}
		
		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Add Edj Shifts')->first()->id;
		$event->save();

	    });
		
		$response = new Response;
		$response->Status = 1;
		return json_encode($response);

	}catch(\Exception $e){
		Log::error($e);
		$response = new Response;
		$response->Status = 0;
		$response->Message = "An Error Occured. Please Consult IT";
		return json_encode($response);
	}
    }

    private function checkmydate($date) {

        $tempDate = explode('/', $date);
        // checkdate(month, day, year)
        return checkdate($tempDate[0], $tempDate[1], $tempDate[2]);
    }

    public function verify_access() {
return true;
        //This page is visible to all drivers
        //return Auth::user()->driver;
    }

	public function indexOld(){
	
        if(!$this->verify_access()) {
            //Log 
            unauthorized('Get Add_Fuel_Log View',Auth::user());
            return view('errors.404');
        }

		$page_title = "Add Fuel Coupon/اضافة قسيمة فيول";
		
		date_default_timezone_set("Asia/Beirut");
		$drivers = EMT::select('name','nickname','id')
							->where('driver',1)
							->where('is_deleted',0)
							->get();
							
		foreach ($drivers as $driver) {
             if($driver->nickname == ""){
                 $driver->nickname = $driver->name;
             }else{
                 $driver->nickname = $driver->nickname . " (" . $driver->name . ")";
			 }
		}
			 
		$stations = FuelStations::select('id','name')->get();	
		
		$cars = Ambulance::select('id','car_number')
							->where('is_deleted',0)
							->get();
		$date = date("m/d/Y");
		$fuel_types = FuelTypes::select('id','description')->get();		
		return view('add_fuel', compact('fuel_types','stations','page_title','drivers','cars','date','fuel_types'));
	
	}


public function generateExcel() {

	if(!Auth::check()) return "Unauthorized access. Please Login";
	
	$edj_admin = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Edj Admin')->first()->id);
	$head_of_station = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id);

	if(!$edj_admin && !$head_of_station) return "Unauthorized access";

	
        $year = Input::get('year');
        $month = Input::get('month');

	if($month == "" || !is_numeric($month) || $month < 0 || $month > 12) 
		return "Month Error. Please Contact IT";

	if($year == "" || !is_numeric($year) ) 
		return "Year Error. Please Contact IT";

        $shifts = \DB::select('SELECT 
                        u.name AS N,
                        d.date AS D,
                        d.type AS T
                    from edj_dtl d 
                    join edj_hdr h on d.edj_hdr_id = h.id 
                    join users u on h.user_id = u.id 
                    where h.is_deleted = 0 
                        and h.month = ' . $month . ' 
                        and h.year = ' . $year . ' 
                    order by d.date,d.type,u.id');

        $dataAM = array();
        $dataPM = array();
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for( $day = 1 ; $day <= $days ; $day++ ){

            $dataAM[] = "";
            $dataPM[] = "";
            $dataCAM[] = "";
            $dataCPM[] = "";
        }

        foreach($shifts as $shift) {

            $index = explode( "-" , $shift->D )[2];
            if ( $shift->T == "AM Shift" ) {

                $dataAM[$index - 1] .= $shift->N . "\n";
            }else if ( $shift->T == "PM Shift" ) {

                $dataPM[$index - 1] .= $shift->N . "\n";
            } else if ( $shift->T == "Technical AM Shift" ) {

                $dataCAM[$index - 1] .= $shift->N . "\n";
            }else if ( $shift->T == "Technical PM Shift" ) {

                $dataCPM[$index - 1] .= $shift->N . "\n";
            }
        }

        Excel::create('Edj_' . $month . "_" . $year, function($excel) use ($year,$month,$dataAM,$dataPM,$dataCAM,$dataCPM) {
            $excel->setTitle('Equipe de Jour');

    // Chain the setters
            $excel->setCreator('IT')
                    ->setCompany('LRC 502');

    // Call them separately
            $excel->setDescription('!!');

            $excel->sheet('Shift Count', function($sheet) use ($year,$month) {
            
                $shift_counts = \DB::select('SELECT count(*) AS C,
                                                    U.name AS N 
                                                FROM edj_dtl D 
                                                JOIN edj_hdr H ON D.edj_hdr_id = H.id 
                                                JOIN users U ON H.user_id = U.id 
                                            WHERE H.is_deleted = 0 
                                                AND H.month= ' . $month . ' 
                                                AND H.year = ' . $year . ' AND D.type in (\'AM Shift\',\'PM Shift\')
                                            GROUP BY U.name
                                            ORDER BY C DESC');

                $count = count($shift_counts);

                foreach($shift_counts as $index => $c) {

                    $sheet->cell('A' . ($index + 2) , function($cell) use ($c) {

                            $cell->setValue($c->N);
                    });

                    $sheet->cell('B' . ($index + 2) , function($cell) use ($c) {

                            $cell->setValue($c->C);
                    });

                }
                
                $sheet->row(1, array(
                    'EMT', 'Shift Count'
                ));

                $sheet->row(1, function($row) {

                    // call cell manipulation methods
                    $row->setBackground('#939FA3');
                    $row->setAlignment('center');
                    $row->setVAlignment('center');
                    $row->setFontSize(16);
                    $row->setFontWeight('bold');
                });
                $sheet->freezeFirstRow();

                $sheet->cells('A2:B' . ($count + 1), function($cells) {

                    $cells->setAlignment('left');
                    $cells->setVAlignment('top');
                    $cells->setFontSize(14);

                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . ($count + 1) , 'thick');


            });
			$excel->sheet('Covid Shift Count', function($sheet) use ($year,$month) {
            
                $shift_counts = \DB::select('SELECT count(*) AS C,
                                                    U.name AS N 
                                                FROM edj_dtl D 
                                                JOIN edj_hdr H ON D.edj_hdr_id = H.id 
                                                JOIN users U ON H.user_id = U.id 
                                            WHERE H.is_deleted = 0 
                                                AND H.month= ' . $month . ' 
                                                AND H.year = ' . $year . ' AND D.type not in (\'AM Shift\',\'PM Shift\')
                                            GROUP BY U.name
                                            ORDER BY C DESC');

                $count = count($shift_counts);

                foreach($shift_counts as $index => $c) {

                    $sheet->cell('A' . ($index + 2) , function($cell) use ($c) {

                            $cell->setValue($c->N);
                    });

                    $sheet->cell('B' . ($index + 2) , function($cell) use ($c) {

                            $cell->setValue($c->C);
                    });

                }
                
                $sheet->row(1, array(
                    'EMT', 'Shift Count'
                ));

                $sheet->row(1, function($row) {

                    // call cell manipulation methods
                    $row->setBackground('#939FA3');
                    $row->setAlignment('center');
                    $row->setVAlignment('center');
                    $row->setFontSize(16);
                    $row->setFontWeight('bold');
                });
                $sheet->freezeFirstRow();

                $sheet->cells('A2:B' . ($count + 1), function($cells) {

                    $cells->setAlignment('left');
                    $cells->setVAlignment('top');
                    $cells->setFontSize(14);

                });
                $sheet->setAutoSize(true);
                $sheet->setBorder("A1:B" . ($count + 1) , 'thick');


            });
            $excel->sheet('Shifts', function($sheet) use ($year,$month,$dataAM,$dataPM,$dataCAM,$dataCPM) {

        // Sheet manipulation
                $sheet->row(1, array(
                    'Monday', 'Tuesday' , 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                ));
                
		$sheet->setAutoSize(true);


		$sheet->setBorder("A1:F7" , 'thick');
 
                $sheet->row(1, function($row) {

                    // call cell manipulation methods
                    $row->setBackground('#939FA3');
                    $row->setAlignment('center');
                    $row->setVAlignment('center');
                    $row->setFontSize(16);
                    $row->setFontWeight('bold');
                });

                $sheet->cells('A2:F7', function($cells) {

                    $cells->setAlignment('left');
                    $cells->setVAlignment('top');
                    $cells->setFontSize(12);

                });

	 	$sheet->getStyle('A2:F7')->getAlignment()->setWrapText(true);
		$sheet->freezeFirstRow();

                $column_width = 25;
                $header_row_height = 30;
                $row_height = 70;
                $sheet->setWidth(array(
                    'A'     =>  $column_width,
                    'B'     =>  $column_width,
                    'C'     =>  $column_width,
                    'D'     =>  $column_width,
                    'E'     =>  $column_width,
                    'F'     =>  $column_width
                ));
                
                $sheet->setHeight(array(
                    1       => $header_row_height,
                    /*2       => $row_height,
                    3       => $row_height,
                    4       => $row_height,
                    5       => $row_height,
                    6       => $row_height,
                    7       => $row_height,*/
                ));

                $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                for($day = 1; $day <= $days; $day++) {

                    $number = date("N", strtotime($year . "-" . $month . "-" . $day));
                    if($number != 7) {

                        $column = $this->columnName($number);
                        $row = $this->weekOfMonth(strtotime($year . "-" . $month . "-" . $day));
    
                        $sheet->cell($column . $row , function($cell) use ($day,$dataAM,$dataPM,$dataCAM,$dataCPM) {

                            $val = $day . "\nAM : \n" . $dataAM[$day - 1] . 
                                   " ----- \nPM : \n" . $dataPM[$day - 1]. 
                                   " ----- \nCovid AM : \n" . $dataCAM[$day - 1]. 
                                   " ----- \nCovid PM : \n" . $dataCPM[$day - 1];
                            $cell->setValue($val);
                        });

                    }
                }

            });

            })->download('xls');
    }

    private function weekOfMonth($when) {

        $week = strftime('%U', $when); // weeks start on Sunday
        $firstWeekOfMonth = strftime('%U', strtotime(date('Y-m-01', $when)));
        return 2 + ($week < $firstWeekOfMonth ? $week : $week - $firstWeekOfMonth);
    }

    private function columnName($number) {

        switch($number) {
            case 1:
                return 'A';
                break;
            case 2:
                return 'B';
                break;
            case 3:
                return 'C';
                break;
            case 4:
                return 'D';
                break;
            case 5:
                return 'E';
                break;
            case 6:
                return 'F';
                break;
        }
    }

    public function view(){

	if(!Auth::check()) return "Unauthorized access. Please Login";
	
	$edj_admin = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Edj Admin')->first()->id);
	$head_of_station = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id);

	if(!$edj_admin && !$head_of_station) return "Unauthorized access";

	$page_title = "Equipe de Jour Reports";
	return view("generate_edj_report",["page_title" => $page_title]);
    }


}
