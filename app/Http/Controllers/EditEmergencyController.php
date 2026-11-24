<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use File;
use Auth;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\ambulance as Ambulance;
use App\Http\ORM\Urgence;
use App\Http\ORM\Nationalities;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\mission_type;
use App\Http\ORM\hospital;
use App\Http\ORM\station;
use App\Http\ORM\location;
use App\Http\ORM\Material;

use App\Http\ORM\MissionMaterial;
use App\Http\ORM\UrgenceChangeLog as CL;

use App\Http\ORM\MissionDocument;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\MissionEMT;

use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

class EditEmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

public function setup_times($s,$urg){



        $date = DateTime::createFromFormat("m\/d\/Y",trim($s->date))->format("Y-m-d");
        $urg->call_time = $date . " " . trim($s->call_h) . ":" . trim($s->call_m) . ":00";
        $dep_to_site = $date . " " . trim($s->dep_s_h) . ":" . trim($s->dep_s_m) . ":00";
        $arr_on_site = $date . " " . trim($s->arr_s_h). ":" . trim($s->arr_s_m) . ":00";
        $dep_from_site = $date . " " . trim($s->dep_f_s_h) . ":" . trim($s->dep_f_s_m) . ":00";
        $arr_to_dest = $date . " " . trim($s->arr_d_h) . ":" . trim($s->arr_d_m) . ":00";
        $unit_available = $date . " " . trim($s->unit_available_h) . ":" . trim($s->unit_available_m) . ":00";
        $ret_to_centre = $date . " " . trim($s->ret_s_h) . ":" . trim($s->ret_s_m) . ":00";

        if($dep_to_site < $urg->call_time){
            $urg->dep_to_site = DateTime::createFromFormat('Y-m-d H:i:s',$dep_to_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $arr_on_site = DateTime::createFromFormat('Y-m-d H:i:s',$arr_on_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $dep_from_site = DateTime::createFromFormat('Y-m-d H:i:s',$dep_from_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $arr_to_dest = DateTime::createFromFormat('Y-m-d H:i:s',$arr_to_dest)->modify("+1 day")->format('Y-m-d H:i:s');
            $unit_available = DateTime::createFromFormat('Y-m-d H:i:s',$unit_available)->modify("+1 day")->format('Y-m-d H:i:s');
            $ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->dep_to_site = $dep_to_site;
        }

        if($arr_on_site < $urg->dep_to_site){
            $urg->arr_on_site = DateTime::createFromFormat('Y-m-d H:i:s',$arr_on_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $dep_from_site = DateTime::createFromFormat('Y-m-d H:i:s',$dep_from_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $arr_to_dest = DateTime::createFromFormat('Y-m-d H:i:s',$arr_to_dest)->modify("+1 day")->format('Y-m-d H:i:s');
            $unit_available = DateTime::createFromFormat('Y-m-d H:i:s',$unit_available)->modify("+1 day")->format('Y-m-d H:i:s');
            $ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->arr_on_site = $arr_on_site;
        }

        if($dep_from_site < $urg->arr_on_site){
            $urg->dep_from_site = DateTime::createFromFormat('Y-m-d H:i:s',$dep_from_site)->modify("+1 day")->format('Y-m-d H:i:s');
            $arr_to_dest = DateTime::createFromFormat('Y-m-d H:i:s',$arr_to_dest)->modify("+1 day")->format('Y-m-d H:i:s');
            $unit_available = DateTime::createFromFormat('Y-m-d H:i:s',$unit_available)->modify("+1 day")->format('Y-m-d H:i:s');
            $ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->dep_from_site = $dep_from_site;
        }

        if($arr_to_dest < $urg->dep_from_site){
            $urg->arr_to_dest = DateTime::createFromFormat('Y-m-d H:i:s',$arr_to_dest)->modify("+1 day")->format('Y-m-d H:i:s');
            $unit_available = DateTime::createFromFormat('Y-m-d H:i:s',$unit_available)->modify("+1 day")->format('Y-m-d H:i:s');
            $ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->arr_to_dest = $arr_to_dest;
        }

        if($unit_available < $urg->arr_to_dest){
            $urg->unit_available = DateTime::createFromFormat('Y-m-d H:i:s',$unit_available)->modify("+1 day")->format('Y-m-d H:i:s');
            $ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->unit_available = $unit_available;
        }

        if($ret_to_centre < $urg->unit_available){
            $urg->ret_to_centre = DateTime::createFromFormat('Y-m-d H:i:s',$ret_to_centre)->modify("+1 day")->format('Y-m-d H:i:s');
        }else{
            $urg->ret_to_centre = $ret_to_centre;
        }
        return $urg;
    }

    private function cl($field,$old,$new,$id) {

        $c = new CL;
        $c->field = $field;
        $c->old_value = $old;
        $c->new_value = $new;
        $c->user_id = Auth::user()->id;
	$c->mission_id = $id;
        return $c; 
    }

    private function get_level($urg,$user_id) {
		$user_level = 0 ;
	//Check if user was in mission
		$emts = $urg->emts()->pluck('emt_id')->toArray();
		if(in_array($user_id,$emts)) {

			$user_level = 1;
		}
		
if(!is_null($urg->driver()->first())) {
		$driver = $urg->driver()->first()->driver_id;
		if($driver == $user_id) {

			$user_level = 2;
		} 
}
		//Check if user is mission leader
if(!is_null($urg->leader()->first())) {
		$ml = $urg->leader()->first()->mission_leader_id;
		if($ml == $user_id) {

			$user_level = 3;
		} 
}
		//Check if user is team leader
		if(EMT::where('id',$user_id)->first()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Chef Equipe')->first()->id)) {

			$user_level = 4;
		}
		//Check if user is head of station
		if(EMT::where('id',$user_id)->first()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id)) {

			$user_level = 5;
		}
		return $user_level;

    }
    private function verify_update_privilege($urg){

	try {
	
		$user_level = 0;
		$updated_by = 0;
		$user_id = Auth::user()->id;

		//First step check if user has edit capabilities
		$user_level = $this->get_level($urg,$user_id);			
//Log::warning("User Level " . $user_level);
		//Check if user can edit
		if($user_level == 0) {

			return -1;//Unaithorized access
		}
		
		//Check if User is past time
		if($user_level < 5) {
			$dt = new DateTime();
			$old = DateTime::CreateFromFormat('Y-m-d H:i:s',Urgence::where('id',$urg->id)->first()->created_at);
			$diff = $dt->diff($old);
			$hours = $diff->h;
			$hours = $hours + ($diff->days*24);
			if($hours > 24) {

				return -2;//Past 24 hours
			}
		}

		//Check if already edited
		$edit_ids = CL::where('mission_id',$urg->id)->pluck('user_id');
		$max_level = 0;
		foreach($edit_ids as $id) {
//Log::warning("Edit Ids " . $edit_ids);
			$temp_level = $this->get_level($urg,$id);
			if($temp_level > $max_level) {

				$max_level = $temp_level;
			}
		}

		if($max_level > $user_level) {

			return -3;//Edited by someone with higher privilege
		}

		return 1;//Success
	} catch (\Exception $e) {
		Log::error($e);
		return 0;//Failure
	}

    }

	public function unlink_doc() {

		$s = Request::get('form');

		try {

			$urg = Urgence::where('id',$s['urg_id'])->first();

			$edit = $this->verify_update_privilege($urg);
			if($edit == -3) {

				$response = new Response;
				$response->Status = 0;
				$response->Message = "This Urgence was updated by a user with higher privilege.Contact Team Leader or Head of Station.";
				return json_encode($response);
			} else if($edit == -2) {

				$response = new Response;
				$response->Status = 0;
				$response->Message = "This Urgence was added more than a day ago. Please Contact Head of Station.";
				return json_encode($response);
			} else if($edit == -1) {

				$response = new Response;
				$response->Status = 0;
				$response->Message = "You don't have the ability to edit this mission, please contact any EMT in this mission, or any Team Leader.";
				return json_encode($response);
			} else if($edit == 0) {

				$response = new Response;
				$response->Status = 0;
				$response->Message = "Something went wrong. Please Contact IT";
				return json_encode($response);
			} if($edit == 1) {
				//Go on
				$response = new Response;
				$response->Status = 0;
				$response->Message = "This Urgence was updated by a user with higher privilege.Contact Team Leader or Head of Station";
				//return json_encode($response);
			}

			\DB::transaction(function() use ($s) {

				$this->cl('Unlink Document',$s['doc_id'],null,$s['urg_id'])->save();
				$doc = MissionDocument::where('id',$s['doc_id'])->first();
				$doc->is_deleted = 1;
				$doc->save();
			});

			$response = New Response;
            		$response->Status = 1;
	    		$response->Message= "Image unlinked succesfully";
            		return json_encode($response);

		} catch (\Exception $e) {
			$response = New Response;
            		$response->Status = 0;
	    		$response->Message= "An Error Has Occured. Please Contact IT";
            		Log::error($e);
            		return json_encode($response);
		}

	
	}
 
    public function store(){

        $s = json_decode(Request::get('form'));
        //Store
        File::append(storage_path('logs/Edit_Emergency_Log.txt'),json_encode($s) . "\n");


        $urgs = array();

        try{

	    $urg = Urgence::where('id',$s->id)->first();

		$edit = $this->verify_update_privilege($urg);
		if($edit == -3) {

			$response = new Response;
			$response->Status = 0;
			$response->Message = "This Urgence was updated by a user with higher privilege.Contact Team Leader or Head of Station.";
			return json_encode($response);
		} else if($edit == -2) {

			$response = new Response;
			$response->Status = 0;
			$response->Message = "This Urgence was added more than a day ago. Please Contact Head of Station.";
			return json_encode($response);
		} else if($edit == -1) {

			$response = new Response;
			$response->Status = 0;
			$response->Message = "You don't have the ability to edit this mission, please contact any EMT in this mission, or any Team Leader.";
			return json_encode($response);
		} else if($edit == 0) {

			$response = new Response;
			$response->Status = 0;
			$response->Message = "Something went wrong. Please Contact IT";
			return json_encode($response);
		} if($edit == 1) {
			//Go on
			$response = new Response;
			$response->Status = 0;
			$response->Message = "This Urgence was updated by a user with higher privilege.Contact Team Leader or Head of Station";
			//return json_encode($response);
		}

            \DB::transaction(function() use ($s,$urg) {

                

                if(isset($s->car_number)) {
                    if($urg->ambulance_id != $s->car_number){

                        $this->cl('Ambulance Number',$urg->ambulance_id,$s->car_number,$s->id)->save();
                        $urg->ambulance_id = $s->car_number;
                    }
                }else if(!is_null($urg->ambulance_id)) {

			$this->cl('Ambulance Number',$urg->ambulance_id,'null',$s->id)->save();
			$urg->ambulance_id = null;
		}

                if(isset($s->driver)) {
		    if(!is_null($urg->driver()->first())){
                    if($urg->driver()->first()->driver_id != $s->driver){

                        $this->cl('Driver',$urg->driver()->first()->driver_id,$s->driver,$s->id)->save();
                        $d = $urg->driver()->first();
			if(is_null($d)){
				$d = new MissionDriver;
			}
	    		//$d = new MissionDriver;
	                $d->driver_id = trim($s->driver);
			\DB::update('update mission_driver set driver_id = ? where mission_id = ? ' , [ $d->driver_id , $urg->id ]);
        	        //$urg->driver()->save($d);
                    }}else{
                        $this->cl('Driver','null',$s->driver,$s->id)->save();
                        $d = $urg->driver()->first();
			if(is_null($d)){
			//	$d->delete();
	    		$d = new MissionDriver;
			}
	                $d->driver_id = trim($s->driver);
			\DB::insert('insert into mission_driver ( driver_id , mission_id ) values ( ? , ? ) ' , [ $d->driver_id , $urg->id ] );
        	        
        	        //$urg->driver()->save($d);

		    }
                }

                if(isset($s->m_leader)) {

		    if($s->m_leader == -1) {

			if(is_null($urg->leader()->first())) {
                        	//Nothing Changes
		    	}
		    	else if($urg->leader()->first()->mission_leader_id != $s->m_leader){

                        	$this->cl('Mission Leader',$urg->leader()->first()->mission_leader_id,'No Mission Leader',$s->id)->save();
                        	$d = $urg->leader()->first();
				if(!is_null($d))
					$d->delete();
                    	}
		    }else {
                    if(is_null($urg->leader()->first())) {
                        $this->cl('Mission Leader','null',$s->m_leader,$s->id)->save();
                        $d = $urg->leader()->first();
			if(!is_null($d))
				$d->delete();
	    		$d = new MissionLeader;
	                $d->mission_leader_id = trim($s->m_leader);
        	        $urg->leader()->save($d);
		    }
		    else if($urg->leader()->first()->mission_leader_id != $s->m_leader){

                        $this->cl('Mission Leader',$urg->leader()->first()->mission_leader_id,$s->m_leader,$s->id)->save();
                        $d = $urg->leader()->first();
			if(!is_null($d))
				$d->delete();
	    		$d = new MissionLeader;
	                $d->mission_leader_id = trim($s->m_leader);
        	        $urg->leader()->save($d);
                    }}
                }

                $emts = $urg->emts()->get();
                $curr = array();
                foreach($emts as $emt) {

                    $curr[] = $emt->emt_id;
                }
		if(!isset($s->emt)) {

		    $s->emt = array();
		}else if(!is_array($s->emt)) {

		    $s->emt = (array)$s->emt;
		}

                if( count ( array_diff( $curr, $s->emt )) != 0 || count ( array_diff( $s->emt, $curr )) != 0 ){

                    $this->cl('EMTs',implode(" ",$curr),implode(" ",$s->emt),$s->id)->save();
		    
		    $urg->emts()->delete();
		    foreach($s->emt as $new) {
			$n = new MissionEMT;
			$n->emt_id = trim(trim(trim($new,"")),"");
                	$urg->emts()->save($n);
		    }
                }

		if( trim($s->other_emts) != $urg->other_emts) {

			$this->cl('Other EMTs',$urg->other_emts, trim($s->other_emts) ,$s->id)->save();
			$urg->other_emts = trim($s->other_emts);
		}

	if(!isset($s->call_h)) {
	
		$s->call_h = $s->dep_s_h;
		$s->call_m = $s->dep_s_m;
		$s->arr_s_h = $s->unit_available_h;
		$s->arr_s_m = $s->unit_available_m;

		$s->dep_f_s_h = $s->unit_available_h;
		$s->dep_f_s_m = $s->unit_available_m;

		$s->arr_d_h = $s->unit_available_h;
		$s->arr_d_m = $s->unit_available_m;

		$s->ret_s_h = $s->unit_available_h;
		$s->ret_s_m = $s->unit_available_m;

	}

		$timeflag = false;
		if(date('m/d/Y',strtotime($urg->call_time)) != $s->date ) {

			$this->cl('Date', date('m/d/Y',strtotime($urg->call_time)) , $s->date ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->call_time)) != $s->call_h . ":" . $s->call_m ) {

			$this->cl('Call Time', date('G:i',strtotime($urg->call_time)) , $s->call_h . ":" . $s->call_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->dep_to_site)) != $s->dep_s_h . ":" . $s->dep_s_m ) {

			$this->cl('Departure to site Time', date('G:i',strtotime($urg->dep_to_site)) , $s->dep_s_h . ":" . $s->dep_s_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->arr_on_site)) != $s->arr_s_h . ":" . $s->arr_s_m ) {

			$this->cl('Arrival on site Time', date('G:i',strtotime($urg->arr_on_site)) , $s->arr_s_h . ":" . $s->arr_s_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->dep_from_site)) != $s->dep_f_s_h . ":" . $s->dep_f_s_m ) {

			$this->cl('Departure from site Time', date('G:i',strtotime($urg->dep_from_site)) , $s->dep_f_s_h . ":" . $s->dep_f_s_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->arr_to_dest)) != $s->arr_d_h . ":" . $s->arr_d_m ) {

			$this->cl('Arrival to destination Time', date('G:i',strtotime($urg->arr_to_dest)) , $s->arr_d_h . ":" . $s->arr_d_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->unit_available)) != $s->unit_available_h . ":" . $s->unit_available_m ) {

			$this->cl('Unit available Time', date('G:i',strtotime($urg->unit_available)) , $s->unit_available_h . ":" . $s->unit_available_m ,$s->id)->save();
			$timeflag = true;
		}

		if(date('G:i',strtotime($urg->ret_to_centre)) != $s->ret_s_h . ":" . $s->ret_s_m ) {

			$this->cl('Return to Station Time', date('G:i',strtotime($urg->ret_to_centre)) , $s->ret_s_h . ":" . $s->ret_s_m ,$s->id)->save();
			$timeflag = true;
		}

		if($timeflag == true) {

			$urg = $this->setup_times($s,$urg);
		}


		if(isset($s->mission_type)) {

			if($s->mission_type != $urg->type) {

				$this->cl('Mission Type', $urg->type , $s->mission_type ,$s->id)->save();
				$urg->type = trim($s->mission_type);
			}
		}
		
		if(!isset($s->annuler)) {

			if( trim($s->fname) . " %%% " . trim($s->lname) != $urg->patient_name ) {

				$this->cl('Patient Name',$urg->patient_name, trim($s->fname) . " %%% " . trim($s->lname) ,$s->id)->save();
				$urg->patient_name = trim($s->fname) . " %%% " . trim($s->lname);
			} 

			if( trim($s->year) != $urg->patient_age ) {

				$this->cl('Patient Age',$urg->patient_age, trim($s->year) ,$s->id)->save();
				$urg->patient_age = trim($s->year);
			} 
			
			if( trim ( $s->sex ) == 1 ) $s->sex = 'Male';
			else $s->sex = 'Female';

			if( trim($s->sex) != $urg->patient_sex) {

				$this->cl('Patient Sex',$urg->patient_sex, trim($s->sex) ,$s->id)->save();
				$urg->patient_sex = trim($s->sex);
			} 

			if( trim($s->nationality) != $urg->patient_nationality ) {

				$this->cl('Patient Nationality',$urg->patient_nationality, trim($s->nationality) ,$s->id)->save();
				$urg->patient_nationality = trim($s->nationality);
			} 

			if( trim($s->chief_complaint) != $urg->patient_case ) {

				$this->cl('Chief Complaint',$urg->patient_case, trim($s->chief_complaint) ,$s->id)->save();
				$urg->patient_case = trim($s->chief_complaint);
			} 
			
			if(isset($s->material_2)) $s->material_2 = (array) $s->material_2;
			else $s->material_2 = array();
			if( count ( array_diff( $s->material_2, $urg->material()->pluck('material_id')->toArray() )) != 0 || count ( array_diff( $urg->material()->pluck('material_id')->toArray(), $s->material_2 )) != 0 ) {

			//if( sort($s->material_2) != $urg->material()->pluck('material_id') ) {

				$mats = $urg->material()->get();

				//$this->cl('Material Used',$urg->material_used, trim($s->material) )->save();
				foreach($mats as $mat) {
					$mat->delete();
				}
				foreach($s->material_2 as $mat) {
					$m = new MissionMaterial;
					$m->mission_id = $s->id;
					$m->material_id = $mat;
					$m->save();
				}
			}

			/*if( trim($s->material) != $urg->material_used ) {

				$this->cl('Material Used',$urg->material_used, trim($s->material) ,$s->id)->save();
				$urg->material_used = trim($s->material);
			} */

			if( trim($s->o2) != $urg->o2 ) {

				$this->cl('O2',$urg->o2, trim($s->o2) ,$s->id)->save();
				$urg->o2 = trim($s->o2);
			} 
			if( trim($s->phone_number) != $urg->phone ) {

				$this->cl('Phone Number',$urg->phone, trim($s->phone_number) ,$s->id)->save();
				$urg->phone = trim($s->phone_number);
			} 
		}else {

			if( !is_null($urg->patient_name) ) {

				$this->cl('Patient Name',$urg->patient_name, 'null' ,$s->id)->save();
				$urg->patient_name = null;
			} 

			if( !is_null($urg->patient_age) ) {

				$this->cl('Patient Age',$urg->patient_age, 'null' ,$s->id)->save();
				$urg->patient_age = null;
			} 
			
			if( !is_null($urg->patient_sex) ) {

				$this->cl('Patient Sex',$urg->patient_sex, 'null' ,$s->id)->save();
				$urg->patient_sex = null;
			} 

			if( !is_null($urg->patient_nationality ) ) {

				$this->cl('Patient Nationality',$urg->patient_nationality, 'null' ,$s->id)->save();
				$urg->patient_nationality = null;
			} 

			if( !is_null($urg->patient_case ) ) {

				$this->cl('Chief Complaint',$urg->patient_case, 'null' ,$s->id)->save();
				$urg->patient_case = null;
			} 

			/*if( !is_null($urg->material_used ) ) {

				$this->cl('Material Used',$urg->material_used, 'null' ,$s->id)->save();
				$urg->material_used = null;
			} */
			

			$mats = $urg->material()->get();
			foreach($mats as $mat) {
				$mat->delete();
			}


			if( !is_null($urg->o2 ) ) {

				$this->cl('O2',$urg->o2, 'null' ,$s->id)->save();
				$urg->o2 = null;
			} 
			if( !is_null($urg->phone ) ) {

				$this->cl('Phone Number',$urg->phone, 'null' ,$s->id)->save();
				$urg->phone = null;
			}
		}

		if(isset($s->other_emts)) {

			if($s->other_emts != $urg->other_emts) {

				$this->cl('Other EMTs', $urg->other_emts, $s->other_emts ,$s->id)->save();
				$urg->other_emts = trim($s->other_emts);
			}
		}

		if( is_null($urg->from_other) ) {

			if ($urg->from != trim($s->from) . "." . trim($s->from_specific)) {

				if($s->from != 4){
					$this->cl('From', $urg->from, trim($s->from) . "." . trim($s->from_specific) ,$s->id)->save();
					$urg->from = trim($s->from) . "." . trim($s->from_specific);
				}else{
					$this->cl('From', $urg->from, trim($s->from_specific_2) ,$s->id)->save();
					$urg->from = 4;
					$urg->from_other = trim($s->from_specific_2);
				}		
			}
		}else {
			if ($s->from != 4) {

				$this->cl('From', $urg->from_other, trim($s->from) . "." . trim($s->from_specific) ,$s->id)->save();
				$urg->from = trim($s->from) . "." . trim($s->from_specific);
				$urg->from_other = null;
			}else{
			
				$this->cl('From', $urg->from_other, trim($s->from_specific_2) ,$s->id)->save();
				$urg->from_other = trim($s->from_specific_2);
			}	
		}

		if( is_null($urg->to_other) ) {

			if ($urg->to != trim($s->to) . "." . trim($s->to_specific)) {

				if($s->to != 4){
					$this->cl('To', $urg->to, trim($s->to) . "." . trim($s->to_specific) ,$s->id)->save();
					$urg->to = trim($s->to) . "." . trim($s->to_specific);
				}else{
					$this->cl('To', $urg->to, trim($s->to_specific_2) ,$s->id)->save();
					$urg->to = 4;
					$urg->to_other = trim($s->to_specific_2);
				}		
			}
		}else {
			if ($s->to != 4) {

				$this->cl('To', $urg->to_other, trim($s->to) . "." . trim($s->to_specific) ,$s->id)->save();
				$urg->to = trim($s->to) . "." . trim($s->to_specific);
				$urg->to_other = null;
			}else{
			
				$this->cl('To', $urg->to_other, trim($s->to_specific_2) ,$s->id)->save();
				$urg->to_other = trim($s->to_specific_2);
			}	
		}

		if(isset($s->comment)) {
			if($urg->comment != $s->comment) {
				$this->cl('Comment', $urg->comment, trim($s->comment),$s->id)->save();
				$urg->comment = trim($s->comment);
			}
		}

		$urg->save();
		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Edit Emergency')->first()->id;
		$event->save();

            });


        }catch(\Exception $e){
            $response = New Response;
            $response->Status = 0;
	    $response->Message= "An Error Has Occured. Please Contact IT";
            Log::error($e);
            return json_encode($response);
        }
        $response = New Response;
        $response->Status = 1;
        return json_encode($response);
        
    }



	public function index(){
	
		$id = Input::get('id');
        if(is_null($id)) 
            return view('errors.404');
		
        //Check Permission
        $record = Urgence::where('id',$id)->first();
        
        //Check if exists
        if(is_null($record)) {
            Log::warning('User ' . Auth::user()->nickname . ' tried accesing urgence ' . $id . ' which is not available');
            return view('errors.404');
        }
        

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

        $emts = $record->emts()->get();
        $record->emt_count = count($emts);
        if(count($emts) > 0) {
            $ids = array();
            foreach($emts as $emt){
                $ids[] = $emt->emt_id;
            }
            $record->emts = $ids;
        }else {
            $record->emts = array();
        }
        $page_title = "Edit Emergency";
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

        $date = explode("-",explode(" ",$record->call_time)[0])[1] . "/" . explode("-",explode(" ",$record->call_time)[0])[2] . "/" .explode("-",explode(" ",$record->call_time)[0])[0]; 

        $record->call_time = explode(" ",$record->call_time)[1];
        $record->dep_to_site = explode(" ",$record->dep_to_site)[1];
        $record->arr_on_site = explode(" ",$record->arr_on_site)[1];
        $record->dep_from_site = explode(" ",$record->dep_from_site)[1];
        $record->arr_to_dest = explode(" ",$record->arr_to_dest)[1];
        $record->unit_available = explode(" ",$record->unit_available)[1];
        $record->ret_to_centre = explode(" ",$record->ret_to_centre)[1];

        if(is_null($record->patient_name) || $record->patient_name == "")
            $record->annuler = true;
	else
	    $record->annuler = false;

	$record->material = $record->material()->get()->pluck('material_id')->toArray();
//Log::warning($record->material);
	$material = Material::where('is_deleted',0)->get();

        $hospitals = hospital::select('id','code','name')->orderBy('weight')->get();

        $locations = location::select('id','village','station')->orderBy('village')->get();

        $stations = station::select('name','id')->orderBy('id')->get();

	$documents = $record->documents()->where('is_deleted',0)->get();
	
		if($record->checked == 1){
			$checked_user = EMT::where('id',$record->checked_by)->first() ;
			if($checked_user != null){
				$record->checked_by_user = $checked_user->nickname;
			}
		}

        return view('edit_emergency', compact('record','nationalities','page_title', 'emts', 'ambulances', 'drivers', 'mission_subtypes', 'current_year', 'material', 'locations', 'hospitals', 'date', 'stations', 'documents'));

        //return json_encode($emergency->users());
        //Created by me?

//		return view('edit_fuel', compact('material','fuel_types','stations','page_title','drivers','cars','date','fuel_types'));
 
		
	}
	
	public function checkEmergency() {
		
		$s = json_decode(Request::get('id'));
        $urgs = array();

        try{

            if( Auth::user() && Auth::user()->memberOf('Chef Equipe')) {

				$urg = Urgence::where('id',$s)->first();
				
				if( $urg == null ){
					$response = new Response;
					$response->Status = 0;
					$response->Message = "Urgence not Found."; 
					return json_encode($response);
				}
				if( $urg->checked == 1 ){
					$response = new Response;
					$response->Status = 0;
					$response->Message = "Urgence already Checked."; 
					return json_encode($response);
				}
				if( now()->diffInHours($urg->created_at) > 48 && !Auth::user()->memberOf('Head of Station') ){
					$response = new Response;
					$response->Status = 0;
					$response->Message = "More than 48 Hours have Passed."; 
					return json_encode($response);
				}

				\DB::transaction(function() use ($urg) {

					$urg->checked = 1;
					$urg->checked_by = Auth::user()->id;
					$urg->checked_date = now();
					$urg->save();

					$c = new CL;
					$c->field = "Check Urgence";
					$c->old_value = "";
					$c->new_value = "";
					$c->user_id = Auth::user()->id;
					$c->mission_id = $urg->id;
					$c->save();

				});
				
				$response = new Response;
				$response->Status = 1;
				return json_encode($response);

			}else {
				$response = new Response;
				$response->Status = 0;
				$response->Message = "Insufficient Access Level"; 
				return json_encode($response);
			}

		}catch(\Exception $e){
			$response = New Response;
			$response->Status = 0;
			$response->Message = "An Error Has Occured. Please Consult IT";
			Log::error($e);
			return json_encode($response);
		}
	}
}
