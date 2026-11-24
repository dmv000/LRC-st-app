<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\fuel_stations as FuelStation;

use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use Illuminate\Support\Facades\Log;

class AddFuelStationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store() {

	$s = json_decode(Request::get('form'));
	File::append(storage_path('logs/Add_Fuel_Station_Log.txt'),json_encode($s)."\n");

        if(!$this->verify_access()) {
            //Log 
            unauthorized('Add_Fuel_Station',Auth::user());
            $response = new Response;
	    $response->Status = 0;
	    $response->Message = "Unauthorized Access";

	    $ev = new Event();
	    $ev->create('Unauthorized Access','Add Fuel Station');

	    return json_encode($response);
        }


	try {
	
	    \DB::transaction(function() use ($s) {
		$response = new Response;
		
		$log = new FuelStation;
		
		if(isset($s->fuel_station) && $s->fuel_station != null && $s->fuel_station != "") {

		    $log->name = $s->fuel_station;
		}else{

		    $response->Status = 0;
		    $response->Message = "Fuel Station Name Error";
		    Log::warning(json_encode($response));
		    return json_encode($response);
		}


		if(isset($s->commercial_number) && $s->commercial_number != null && $s->commercial_number != "") {

		    $log->commercial_number = $s->commercial_number;
		}else{

		    $response->Status = 0;
		    $response->Message = "Fuel Commercial Number Error";
		    Log::warning(json_encode($response));
		    return json_encode($response);
		}
		
		$log->save();

		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Add Fuel Station')->first()->id;
		$event->save();

	    });
		
		$response = new Response;
		$response->Status = 1;
		return json_encode($response);

	}catch(\Exception $e){
		Log::error($e);
		$response = new Response;
		$response->Status = 0;
		$response->Message = $e->getMessage();
		return json_encode($response);
	}

    }

    
    public function verify_access() {
//return true;
        //This page is visible to all drivers
        return Auth::user()->driver;
    }

	public function index(){
	
        if(!$this->verify_access()) {
            //Log 
            //unauthorized('Get Add_Fuel_Station View',Auth::user());
	    $ev = new Event();
	    $ev->create('Unauthorized Access','Add Fuel Station View');

            return view('errors.404');
        }
	    $ev = new Event();
	    $ev->create('Add Fuel Station','');

		$page_title = "Add Fuel Station";
		return view('add_fuel_station', compact('page_title'));
	
	}

}
