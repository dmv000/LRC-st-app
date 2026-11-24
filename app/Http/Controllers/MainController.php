<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;

use App\User as EMT;
use App\Http\ORM\ambulance;
use App\Http\ORM\mission_type;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\hospital;
use App\Http\ORM\location;
use App\Http\ORM\station;
use App\Http\ORM\Nationalities;
use App\Http\ORM\Urgence;
use App\Http\ORM\Material;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Input;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        date_default_timezone_set("Asia/Beirut");
        $page_title = "Add Emergency";
        // start by figuring out the date range
        $emts = EMT::select('name','nickname','id','driver')->where('is_deleted',0)->orderBy('name','asc')->get();
        $drivers = [];//emergency_medical_provider::select('first_name','last_name','nickname','id')->where('driver','1')->get();
        $ambulances = ambulance::select('id','car_number as number')
                                ->where('is_deleted','0')
                                ->get();

        $nationalities = Nationalities::select('id','name')->orderBy('weight','desc')->orderBy('name')->get();

        $mission_subtypes = mission_subtype::select('id',
                                                     'description')
                                                     ->get();
	$material = Material::where('is_deleted',0)->get();

         foreach ($emts as $emt) {
             if($emt->nickname == ""){
                 $emt->nickname = $emt->name;
             }else{
                 $emt->nickname = $emt->nickname . " (" . $emt->name . ")";
             }
             if($emt->driver == "1"){
                 $drivers[] = $emt;
             }
         }

        foreach ($mission_subtypes as $mission_subtype) {
            $mission_types = mission_type::select('id','name')
                                            ->where('mission_subtype',$mission_subtype->id)
                                            ->get();
            $mission_subtype->mission_types = $mission_types;
        }

        $current_year = date("Y");

        $date = date("m/d/Y");

       
        $hospitals = hospital::select('id','code','name')->orderBy('weight')->get();

        $locations = location::select('id','village','station')->orderBy('village')->get();

        $stations = station::select('name','id')->orderBy('id')->get();
	
	try{
		if(Input::get('parent_id') != "") {
			$record = Urgence::/*select('parent_id')->*/where('id',Input::get('parent_id'))->first();
            $driver = $record->driver()->first();
            if(!is_null($driver)) {
                $record->driver = $driver->driver_id;
            }else {
                $record->driver = -1;
            }
            $ml = $record->leader()->first();
            if(!is_null($ml)) {
                $record->ml = $ml->mission_leader_id;
            }else {
                $record->ml = -1;
            }

            $emtss = $record->emts()->get();
            $record->emt_count = count($emtss);
            if(count($emtss) > 0) {
                $ids = array();
                foreach($emtss as $emt){
                    $ids[] = $emt->emt_id;
                }
                $record->emts = $ids;
            }else {
                $record->emts = array();
            }
        
            $date = explode("-",explode(" ",$record->call_time)[0])[1] 
				. "/" . explode("-",explode(" ",$record->call_time)[0])[2] 
				. "/" .explode("-",explode(" ",$record->call_time)[0])[0]; 

            $record->call_time = explode(" ",$record->call_time)[1];
            $record->dep_to_site = explode(" ",$record->dep_to_site)[1];
            $record->arr_on_site = explode(" ",$record->arr_on_site)[1];
            $record->dep_from_site = explode(" ",$record->dep_from_site)[1];
            $record->arr_to_dest = explode(" ",$record->arr_to_dest)[1];
            $record->unit_available = explode(" ",$record->unit_available)[1];
            $record->ret_to_centre = explode(" ",$record->ret_to_centre)[1];

		}else {
			$record =  null;
		}
	}catch (\Exception $e){
		Log::error($e);
		return view('errors.404');
	}

        return view('add_emergency', compact('material','record','nationalities','page_title', 'emts', 'ambulances', 'drivers', 'mission_subtypes', 'current_year', 'material', 'locations', 'hospitals', 'date', 'stations'));
    }
}
