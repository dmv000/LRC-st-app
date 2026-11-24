<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\ambulance as Ambulance;
use App\Http\ORM\fuel_stations as FuelStations;
use App\Http\ORM\fuel_types as FuelTypes;
use App\Http\ORM\FuelCoupon;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use Illuminate\Support\Facades\Log;

class AddFuelLog extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store() {

	$s = json_decode(Request::get('form'));
	File::append(storage_path('logs/Add_Fuel_Log.txt'),json_encode($s)."\n");

	try {
	
	    \DB::transaction(function() use ($s) {
		$response = new Response;
		
		$log = new FuelCoupon;
		
		if(isset($s->car_number) && $s->car_number != "-1") {

		    $log->car_id = $s->car_number;
		}else{

		    $response->Status = 0;
		    $response->Message = "Ambulance ID Error";
		    Log::warning(json_encode($response));
		    return json_encode($response);
		}

		if(isset($s->driver) && $s->driver != "-1") {

		    $log->driver_id = $s->driver;
		}else{

		    $response->Status = 0;
		    $response->Message = "Driver Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->date) && $this->checkmydate($s->date) ){

		    $tempDate = explode('/', $s->date);
		    $log->date = $tempDate[2] . "-" . $tempDate[0] . "-" . $tempDate[1];
		}else{

		    $response->Status = 0;
		    $response->Message = "Date Error";
		    Log::warning($response);
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->fuel_station) && $s->fuel_station!= "-1") {

		    $log->fuel_station_id = $s->fuel_station;
		}else{

		    $response->Status = 0;
		    $response->Message = "Fuel Station Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->fuel_type) && $s->fuel_type != "-1") {

		    $log->fuel_type_id = $s->fuel_type;
		}else{

		    $response->Status = 0;
		    $response->Message = "Fuel Type Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->number) && !empty($s->number) ) {
$count = FuelCoupon::where('coupon_number',(int)$s->number)->where('is_deleted',0)->get()->count();

if($count > 0) {
	$response = new Response;
	$response->Status = 0;
	$response->Message = "Coupon Number Already Exists";
	Log::warning(json_encode($response));
		    			throw new \Exception($response->Message);
}

			try {

				$number = (int)$s->number;
				$log->coupon_number = $number;
			}catch(\Exception $e){

					$response->Status = 0;
		    			$response->Message = "Coupon Number Error";
		    			Log::warning(json_encode($response));
		    			throw new \Exception($reponse->Message);
			}
		}/*else{

		    $response->Status = 0;
		    $response->Message = "Coupon Number Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}*/

		if(isset($s->litres)) {

			try {

				$litres = $s->litres;
				$log->litre = $litres;
			}catch(\Exception $e){

					$response->Status = 0;
		    			$response->Message = "Litres Error";
		    			Log::warning(json_encode($response));
		    			throw new \Exception($reponse->Message);
			}
		}else{

		    $response->Status = 0;
		    $response->Message = "Litres Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->price)) {

			try {

				$price = (int)$s->price;
				$log->price = $price;
			}catch(\Exception $e){

					$response->Status = 0;
		    			$response->Message = "Price Error";
		    			Log::warning(json_encode($response));
		    			throw new \Exception($reponse->Message);
			}
		}else{

		    $response->Status = 0;
		    $response->Message = "Price Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->kms)) {

			try {

				$kms = (int)$s->kms;
				$log->kms = $kms;
			}catch(\Exception $e){

					$response->Status = 0;
		    			$response->Message = "Kilometers Error";
		    			Log::warning(json_encode($response));
		    			throw new \Exception($reponse->Message);
			}
		}else{

		    $response->Status = 0;
		    $response->Message = "Kilometers Error";
		    Log::warning(json_encode($response));
		    throw new \Exception($reponse->Message);
		}

		if(isset($s->army)) {

			$log->army = 1;
		}else {

			$log->army = 0;
		}
		
		$log->created_by = Auth::user()->id;
		$log->save();

		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Add Fuel Coupon')->first()->id;
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

	public function index(){
	
        if(!$this->verify_access()) {
            //Log 
	    $ev = new Event();
	    $ev->create('Unauthorized Access','Add Fuel Coupon View');
            //unauthorized('Get Add_Fuel_Log View',Auth::user());
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

	    $ev = new Event();
	    $ev->create('Add Fuel Coupon View','');
		
		return view('add_fuel', compact('fuel_types','stations','page_title','drivers','cars','date','fuel_types'));
	
	}

}
