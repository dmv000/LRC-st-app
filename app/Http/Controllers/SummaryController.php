<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;

use App\Http\ORM\ambulance;
use App\Http\ORM\mission_type;
use App\Http\ORM\Urgence;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\material;
use App\Http\ORM\hospital;
use App\Http\ORM\location;
use App\Http\ORM\station;

class SummaryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

	public function needcheckups(){
		
		$day = date("D");
		$today = new DateTime();
		if($day == "Sun") {
			$startDate = $today->modify("-7 day")->format("Y-m-d") . " 09:00:00";
		if($day == "Mon") {
			$startDate = $today->modify("-1 day")->format("Y-m-d") . " 09:00:00";
		}else{
			$startDate = $today->modify("-1 day")->format("Y-m-d") . " 18:00:00";
		}
		$cars = ambulance::whereIn('id',Urgence::where('dep_to_site','>=',$startDate)->where('is_deleted',0)->groupBy('ambulance_id')->pluck('ambulance_id'))->pluck('car_number');
		return json_encode($cars);
	}

    function getColor($num) {
    $hash = md5('color' . $num); // modify 'color' to get a different palette
    return array(
        hexdec(substr($hash, 0, 2)), // r
        hexdec(substr($hash, 2, 2)), // g
        hexdec(substr($hash, 4, 2))); //b
	}}

    public function index()
    {


        date_default_timezone_set("Asia/Beirut");
        $page_title = "Page Title";

        /*$array = [];

        $one = new \stdClass();
        $one->label= "Label1";
        $one->value=25;
        $array[] = $one;

        $two = new \stdClass();
        $two->label= "Label2";
        $two->value=75;
        $array[] = $two;*/

        $currentDate = date("Y-m-d");
        $m = mission::where('departure_time','>=',$currentDate)
                                ->where('departure_time','<=',DateTime::createFromFormat('Y-m-d',$currentDate)->modify('+1 day')->format('Y-m-d'))
                                ->get()
                                ->count();


                                        $array = [];
                                        $arrayy = [];
                                        $colors = [];
        $m = \DB::select('call get_graph("2017-06-25", "2017-06-26")');
        $count = 0;
        foreach($m as $mm){
            $array[] = $mm->count;
            $arrayy[] = mission_type::where('id',$mm->id)->first()->name;
            $colors[] = ["#" . $this->getColor($count)[0] . $this->getColor($count)[1] . $this->getColor($count)[2]];
            $count++;
        }
        if(is_null($m)){
            $array = [0];
            $arrayy = ["No Emergencies"];
            $colors = ["#" . $this->getColor(0)[0] . $this->getColor(0)[1] . $this->getColor(0)[2]];
        }

        $days = [];
        $emergs = [];
        for($i=6;$i>=0;$i--){
            $date = DateTime::createFromFormat('Ymd',date("Ymd"))->modify("- " . $i . " days");
            $days[] =  $date->format("l");
            $emergs[] =  mission::where('departure_time','>=',$date->format('Y-m-d'))
                                    ->where('departure_time','<=',$date->modify('+1 day')->format('Y-m-d'))
                                    ->get()
                                    ->count();
        }


        return view('summary', compact('array','page_title','arrayy','days','emergs','colors'));
    }
}
