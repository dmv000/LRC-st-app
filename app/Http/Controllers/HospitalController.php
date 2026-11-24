<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\ORM\hospital as Hospital;
use App\Http\ORM\district;
use App\Http\ORM\station;
use App\Http\ORM\PhoneNumber;
use Illuminate\Support\Facades\Log;

use Auth;
use Request as Req;
use App\Responses\Response;


use App\Http\ORM\Event;
use App\Http\ORM\EventType;

class HospitalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hospitals = Hospital::select('id','name','code','station','longitude','latitude')->orderBy('weight')->get();
        foreach($hospitals as $hospital){
            $hospital->district = substr($hospital->station,0,1) . "00";
        }

        $districts = district::select('id','name')->groupBy('id')->get();
        $stations = station::select('id','name','district')->get();

        return view('hospitals',compact('hospitals','districts','stations'));
    }
	
	private function verify( ) {

		return Auth::check() && Auth::user()->memberOf('Secretariat Members');
    }
	
	public function store(Req $request){
		
		$response = new Response;
		if( !$this->verify()) {
		
			$response->Status = 0;
			$response->Message = "Unauthorized Access";
			return json_encode($response);
		}

		try {
			$data = $request::Input('data');		
			$this->verifyRequest($data);
			$this->saveHospital($data);

			$response->Status = 1;
			$response->Message = "Hospital saved succesfully";
			return json_encode($response);
		} catch( \Exception $e) {
			$response->Status = 0;
			$response->Message = $e->getMessage();
			return json_encode($response);
		}
	}

	private function saveHospital($data) {
		try {
			\DB::transaction(function() use ($data) {
					
				$hospital = new Hospital();
				$hospital->name = $data['hospitalName'];
				$hospital->code = $data['hospitalCode'];
				$hospital->station = $data['stationId'];
				$hospital->created_by = Auth::user()->id;
				$hospital->save();
								
				$event = new Event;
				$event->user_id = Auth::user()->id;
				$event->details = "Add new Hospital " . $hospital->id;
				$event->event_type = EventType::where('description','Add Hospital')->first()->id;
				$event->save();	
			});
		} catch(\Exception $e){
			Log::error($e);
			throw new \Exception("Failed saving new Hospital");
		}
	}

	private function verifyRequest($data) {
		
 
		$districtId = $data['districtId'];
		$stationId = $data['stationId'];
		$hospitalName = $data['hospitalName'];
		$hospitalCode = $data['hospitalCode'];
		
		if($districtId == null || $districtId == -1){
			throw new \Exception('District is required'); 
		}
		
		if($stationId == null || $stationId == -1){
			throw new \Exception('Station is required'); 
		}
		
		if($hospitalName == null || $hospitalName == ''){
			throw new \Exception('Hospital Name is required'); 
		}
		
		if($hospitalCode == null || $hospitalCode == ''){
			throw new \Exception('Hospital Code is required'); 
		}
	}
	
	public function contacts(Req $request){
		
		$response = new Response;
		if( !$this->verify()) {
		
			$response->Status = 0;
			$response->Message = "Unauthorized Access";
			return json_encode($response);
		}

		
		$hosId = $request::Input('hospitalId');
		if($hosId == null){
			$response->Status = 0;
			$response->Message = "Missing Hospital Id";
			return json_encode($response);
		}
		
		$phones = PhoneNumber::select('id','phone')->where('hospital_id',$hosId)->where('is_deleted',0)->get();
		$response->Status = 1;
		$response->Data = json_encode($phones);
		return json_encode($response);
        
	}
	
	public function updateContacts(Req $request){
		
		$response = new Response;
		if( !$this->verify()) {
		
			$response->Status = 0;
			$response->Message = "Unauthorized Access";
			return json_encode($response);
		}

		$data = $request::Input('data');
		$hosId = $data['hospitalId'];
		if($hosId == null){
			$response->Status = 0;
			$response->Message = "Missing Hospital Id";
			return json_encode($response);
		}
		
		if(!isset($data['contacts'])){
			$contacts = [];
		} else {
			$contacts = $data['contacts'];
		}
		
		try {
			\DB::transaction(function() use ($contacts , $hosId) {
							
				$phones = PhoneNumber::select('id','phone')->where('hospital_id',$hosId)->where('is_deleted',0)->get();
				foreach($phones as $phone){
					$deleted = true;
					foreach($contacts as $contact){
						if($phone->id == $contact['id']){
							$deleted = false;
							if($phone->phone != $contact['name']){
								
								$event = new Event;
								$event->user_id = Auth::user()->id;
								$event->details = "Update Contact " . $phone->id . " from " . $phone->phone . " to " . $contact['name'];
								$event->event_type = EventType::where('description','Hospital Contact')->first()->id;
								$event->save();
		
								$phone->phone = $contact['name'];
								$phone->save();
							}
							break;
						}
					}
					if($deleted){
						
						$event = new Event;
						$event->user_id = Auth::user()->id;
						$event->details = "Delete Contact " . $phone->id;
						$event->event_type = EventType::where('description','Hospital Contact')->first()->id;
						$event->save();
						
						$phone->is_deleted=1;
						$phone->save();
					}
				}
				foreach($contacts as $contact){
					if($contact['id']==0){
						
						$event = new Event;
						$event->user_id = Auth::user()->id;
						$event->details = "New Contact for hospital " . $hosId . " phone : " . $contact['name'];
						$event->event_type = EventType::where('description','Hospital Contact')->first()->id;
						$event->save();
						
						$phone = new PhoneNumber();
						$phone->phone = $contact['name'];
						$phone->hospital_id = $hosId;
						$phone->save();
					}
				}				
			});
		} catch (\Exception $e) {
			Log::error($e);
			$response->Status = 0;
			$response->Message = "An Error Has Occured, Please contact IT";
			return json_encode($response);
        }
		
		$response->Status = 1;
		$response->Message = "Contacts Updated Succesfully";
		return json_encode($response);
	}
}
