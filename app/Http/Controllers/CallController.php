<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;
use File;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;


use App\Http\ORM\hospital as Hospital;
use App\Http\ORM\location as Location;
use App\Http\ORM\station as Station;
use App\User;
use App\Http\ORM\ambulance as Ambulance;
use App\Http\ORM\EmergencyCall as Call;
use App\Http\ORM\EmergencyCallHistory as CallHistory;
use App\Http\ORM\EmergencyCallEMT as CallEMT;
use App\Http\ORM\EmergencyCallSource as CallSource;
use App\Http\ORM\EmergencyCallPatient as Patient;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use App\Responses\Response;

class CallController extends Controller {
	
    public function __construct()
    {
        //$this->middleware('auth');
    }
	
	public function search(){
		
		return view('emergency_calls');
	}
	
	public function searchRecord() {
		
		$callId = Request::get('callId');
		$response = New Response;
		
		if( $callId == null || $callId == "") {
			$response->Status = 0;
			$response->Data = "Call Id not received";
            return json_encode($response);
		}
		
		try {
			
			$rec = Call::where('id',$callId)->first();
			if( $rec==null ){
				$response->Status = 0;
				$response->Data = "Call does not exist";
				return json_encode($response);
			}
			
			$urg_emts = CallEMT::where('emergency_call_id',$callId)->get()->pluck('user_id');
			$patients = Patient::select('name','age','cas')->where('emergency_call_id',$callId)->get();
			$rec->patients = $patients;
			$rec->urg_emts = $urg_emts;
			
            $response->Status = 1;
			$response->Data = json_encode($rec);
            return json_encode($response);
			
		} catch ( \Exception $e ) {
			
			Log::warning("Search Emergency Call Record Exception");
			Log::warning($e); 
            $response->Status = 0;
			$response->Data = "Call does not exist";
            return json_encode($response);
		}
	}
	
	public function searchResults(){
		
		$request = Request::get('form');
		$response = New Response;
		try {
			
			$from_time = $request["from_time"];
			$to_time = $request["to_time"];
			
			$records = [];
			$data = Call::whereBetween('call_time' , [ $from_time , $to_time ])->where('is_deleted',0)->get();
			foreach($data as $record){
				$rec = [];
				$amb = Ambulance::where('id',$record->car_number)->first();
				if($amb==null){
					$rec["car_number"] = "N/A";
				} else {
					$rec["car_number"] = $amb->car_number;
				}
				$driver = User::where('id',$record->driver)->first();
				if( $driver==null ){
					$rec["driver"] = "N/A";
				} else {
					$rec["driver"] = $driver->nickname;
				}
				$team_leader = User::where('id',$record->team_leader)->first();
				if( $team_leader==null ){
					$rec["team_leader"] = "N/A";
				} else {
					$rec["team_leader"] = $team_leader->nickname;
				}
				$rec["call_time"] = $record->call_time;
				$rec["emergency_type"] = $record->emergency_type;
				$rec["id"] = $record->id;
				$records[] = $rec;
			}
			$response->Status = 1;
			$response->Data = json_encode($records);
            return json_encode($response);
			
		} catch ( \Exception $e ) {
			
			Log::warning("Search Emergency Call Exception");
			Log::warning($e); 
            $response->Status = 0;
            return json_encode($response);
		}
	}

    public function index()
    {

		date_default_timezone_set("Asia/Beirut");
		$page_title = "Add Emergency";

		$hospitals = Hospital::select('id','code','name')->orderBy('weight')->get();
		$locations = Location::select('id','village','station')->orderBy('village')->get();
		$stations = Station::select('name','id')->orderBy('id')->get();
		$sources = CallSource::get();

		$emts = User::select('name','nickname','id','driver')->where('is_deleted',0)->orderBy('name','asc')->get();
		$drivers = [];
		$ambulances = Ambulance::select('id','car_number as number')->where('is_deleted','0')->get();

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
		 
		if(Input::get('form_id') != "") {
			
			$rec = Call::where('id',Input::get('form_id'))->where('is_deleted',0)->first();
			if( $rec==null ){
				return view('errors.404');
			}
			$urg_emts = CallEMT::where('emergency_call_id',Input::get('form_id'))->get()->pluck('user_id');
			$patients = Patient::select('name','age','cas')->where('emergency_call_id',Input::get('form_id'))->get();
			$rec->patients = $patients;
			$rec->urg_emts = $urg_emts;
		} else {
			$rec = null;
		}
		$record = json_encode($rec);
		
		return view('add_emergency_call' , compact('hospitals','locations','stations','drivers','ambulances','record','emts','sources','patients') );
	}
	
	private function getTime( $times , $name ){
		
		return $times[$name] == "Invalid date" ? null : $times[$name] . ":00" ;
	}
	
	public function deleteRecord(){
		
		$request = Request::get('form');
		$response = New Response;
        try{
			if( !Auth::user() || !Auth::user()->memberOf('Chef Equipe') ){
				//Unauthorized Access
				Log::warning("Delete Emergency Call - Unauthorized Access");
				return null;
			} else {
				$call = Call::where('id' , $request)->first();
				if($call == null){
					Log::warning("Delete Emergency Call - No Such Emergency Call");
					return null;
				} else {
					$call->is_deleted = 1;
					$call->save();
						
					$event = new Event;
					$event->user_id = Auth::user()->id;
					$event->event_type = EventType::where('description','Delete Emergency Call')->first()->id;
					$event->details = $request;
					$event->save();
					
					$response->Status = 1;
					return json_encode($response);
				}
			}
		}catch ( \Exception $e ) {
			
			Log::warning("Delete Emergency Call Exception");
			Log::warning($e); 
            $response->Status = 0;
            return json_encode($response);
		}
	}
	
	public function store(){
		
		$request = Request::get('form');
        File::append(storage_path('logs/Add_Emergency_Call_Log.txt'),json_encode($request) . "\n");
		
		$response = New Response;
			
		try {
			
			$request_id = $request["id"];
			if( $request_id == 0 ){
				
				//New
				$call = new Call;
				if( Auth::user() ){
					$call->created_by = Auth::user()->id;
				}				
				$oldCall = null;
				
			} else {
				
				$call = Call::where('id' , $request_id)->first();
				$oldCall = new CallHistory;
				if( $call == null ) {
					
					$response->Status = 0;
					Log::error('Trying to save non exiting Record');
					return json_encode($response);
				} /*else {
					if( !Auth::user() && $request["autoSave"] == false ){
						$response->Status = 0;
						$response->Message = "You must be logged in to Update Emergency Calls";
						Log::error('Updating Call without Log In');
						return json_encode($response);
					}
				}*/
			}
			
			$call->call_time = self::getTime($request["times"] , "call_time");
			$call->dep_to_site = self::getTime($request["times"] , "dep_to_site");
			$call->arrival_on_site = self::getTime($request["times"] , "arrival_on_site");
			$call->dep_to_dest = self::getTime($request["times"] , "dep_to_dest");
			$call->arrival_to_dest = self::getTime($request["times"] , "arrival_to_dest");
			$call->unit_ready = self::getTime($request["times"] , "unit_ready");
			$call->return_to_station = self::getTime($request["times"] , "return_to_station");
			
			$general = $request["general"];
			$call->emergency_type = $general["type"];
			$call->emergency_source = $general["source"];
			$call->emergency_source_phone = $general["source_phone"];
			$call->someone_waiting = $general["waiting"];
			$call->cause_of_incident = $general["cause_of_incident"];
			
			$location = $request["location"];
			$call->from_type = $location["from"]["type"];
			$call->from_value = $location["from"]["value"];
			$call->to_type = $location["to"]["type"];
			$call->to_value = $location["to"]["value"];
			$call->location_info = $location["comment"];
			
			//$call->cases = $request["cases"]["message"];
			
			$team = $request["team"];
			$call->car_number = $team["car_number"];
			$call->driver = $team["driver"];
			$call->team_leader = $team["team_leader"];
			$call->other_emts = $team["other_emts"];
			
			$support = $request["support"];
			if( $support["anyway"]["enabled"]=="true" ){
				$call->anyway_time = self::getTime($support["anyway"] , "time");
				$call->anyway_resp = $support["anyway"]["responder"];
			}
			if( $support["dc"]["enabled"]=="true" ){
				$call->dc_time = self::getTime($support["dc"] , "time");
				$call->dc_resp = $support["dc"]["responder"];
			}
			if( $support["ambulance"]["enabled"]=="true" ){
				$call->ambulance_time = self::getTime($support["ambulance"] , "time");
				$call->ambulance_resp = $support["ambulance"]["responder"];
			}
			if( $support["sar"]["enabled"]=="true" ){
				$call->sar_time = self::getTime($support["sar"] , "time");
				$call->sar_resp = $support["sar"]["responder"];
			}
			$call->support_info = $support["info"];
			
			$annuler = $request["annuler"];
			if( $annuler["enabled"]=="true" ){
				$call->annuler = true;
				$call->annuler_reason = $annuler["reason"];
				$call->annuler_source = $annuler["source"];
				$call->annuler_time = self::getTime($annuler,"time");
			} else {
				$call->annuler = false;
			}
			
			$dispatch = $request["dispatch"];
			$call->dispatch_1 = $dispatch["dispatch_1"];
			$call->dispatch_2 = $dispatch["dispatch_2"];
			$call->or_1 = $dispatch["or_1"];
			$call->or_2 = $dispatch["or_2"];
			$call->leader = $dispatch["leader"];
			if(isset($dispatch["comment"])){
				$call->comment = $dispatch["comment"];
			}
			
			$transport = $request["transport"];
			if( $transport["enabled"]=="true" ){
				$call->transport_time = self::getTime($transport,"transport_time");
				$call->transport_type = $transport["transport_type"];
				$call->gamma = $transport["gamma"]=="true"?1:0;
				$call->admission = $transport["admission"];
				$call->doctor = $transport["doctor"];
				$call->reason_for_ambulance = $transport["reason_for_ambulance"];
				$call->cleared_ready = $transport["cleared_ready"];
				$call->o2 = $transport["o2"];
				$call->matelas = $transport["matelas"];
				$call->couveuse = $transport["couveuse"];
				$call->respirateur = $transport["respirateur"];
			}
			
			$request_id = \DB::transaction(function() use ($oldCall , $request, $call , $request_id) {
				
				if ( $oldCall != null ){
					
					$oldCall = CallHistory::create($call->toArray());
					$oldCall->parent_id = $call->id;
					$oldCall->updated_by = !Auth::user() ? 0 : Auth::user()->id;
					$oldCall->save();
				}
				//Save Update Record
				$call->save();
				$request_id = $call->id;
				
				//Save EMT Records
				CallEMT::where('emergency_call_id',$request_id)->delete();
				Patient::where('emergency_call_id',$request_id)->delete();
				
				if( $request["team"]["emts"] != null ) {
					if( is_array($request["team"]["emts"]) ){
						foreach( $request["team"]["emts"] as $emt ){
							$callEMT = new CallEMT();
							$callEMT->user_id = $emt;
							$callEMT->emergency_call_id = $request_id;
							$callEMT->save();
						}
					} else {
						$callEMT = new CallEMT();
						$callEMT->user_id = $request["team"]["emts"];
						$callEMT->emergency_call_id = $request_id;
						$callEMT->save();
					}
				}
				
				if( $request["cases"]["count"]>0 && $request["cases"]["data"] != null ) {
					if( is_array($request["cases"]["data"]) ){
						foreach( $request["cases"]["data"] as $cas ){
							$patient = new Patient();
							$patient->name = $cas['name'];
							$patient->age = $cas['age'];
							$patient->cas = $cas['cas'];
							$patient->emergency_call_id = $request_id;
							$patient->save();
						}
					}
				}
				
				return $request_id;
			});
			
			$response->Message = $request_id;
			$response->Status = 1;
			return json_encode($response);
		
		} catch ( \Exception $e ) {
			
			Log::warning("Store Emergency Call Exception");
			Log::warning($e); 
            $response->Status = 0;
            return json_encode($response);
		}
	}
	
}