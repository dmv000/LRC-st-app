<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;
use File;
use Illuminate\Support\Facades\Log;

use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\MissionEMT;
use App\Http\ORM\Urgence;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use App\Http\ORM\MissionMaterial;

use App\Responses\Response;

use App\Jobs\SendEmail;

class add_emergency extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //date_default_timezone_set("Asia/Beirut");
    }

    public function setup_times($s,$urg){

        $date = DateTime::createFromFormat("m\/d\/Y",trim($s->date))->format("Y-m-d");

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

    public function setup_patient_info($s,$urg){
        if(!isset($s->annuler)){
            $urg->patient_name = trim($s->fname) . " %%% " . trim($s->lname);
            $urg->patient_age = trim($s->year);
            $urg->patient_sex = (trim($s->sex) == 1 ? 'Male' : 'Female');
            $urg->patient_nationality = trim($s->nationality);
            $urg->patient_case = trim($s->chief_complaint);
            //$urg->material_used = trim($s->material);
            $urg->o2 = trim($s->o2);
			$urg->phone = trim($s->phone_number);
        }
        return $urg;
    }

    private function setup_from_to($s,$urg){
        if($s->from != 4){
            $urg->from = trim($s->from) . "." . trim($s->from_specific);
        }else{
	    $urg->from = 4;
            $urg->from_other = trim($s->from_specific_2);
        }

        if($s->to != 4){
            $urg->to = trim($s->to) . "." . trim($s->to_specific);
        }else{
	    $urg->to = 4;
            $urg->to_other = trim($s->to_specific_2);
        }
        return $urg;
    }

    public function setup_parties($s,$urg){
        //Driver    
        if(isset($s->driver)){
            $d = new MissionDriver;
            $d->driver_id = trim($s->driver);
            $urg->driver()->save($d);
        }

        //Mission Leader
        if(isset($s->m_leader) && $s->m_leader != -1){
            $d = new MissionLeader;
            $d->mission_leader_id = trim($s->m_leader);
            $urg->leader()->save($d);
        }

        //emts
	if(isset($s->emt)){
        if(is_array($s->emt)){
            foreach ($s->emt as $one) {
                if($one != -1){
                    $d = new MissionEMT;
                    $d->emt_id = trim(trim(trim($one,"")),"");
                    $urg->emts()->save($d);
                }
            }                
        }else{
            if($s->emt != -1){
                $d = new MissionEMT;
                error_log(trim(trim(trim($s->emt,"")),""));
                $d->emt_id = trim(trim(trim($s->emt,"")),"");
                $urg->emts()->save($d);
            }
        }}
    }

    public function store(){

        $s = json_decode(Request::get('form'));
        //Store
        File::append(storage_path('logs/Add_Emergency_Log.txt'),json_encode($s) . "\n");


        $urgs = array();

        try{
                $urg = new Urgence;
                if(isset($s->car_number))
                    $urg->ambulance_id = trim($s->car_number);
                $urg = $this->setup_times($s,$urg);
                $urg->type = trim($s->mission_type);
                $urg = $this->setup_patient_info($s,$urg);
                $urg = $this->setup_from_to($s,$urg);
                if($s->parent_id != null && $s->parent_id != ""){
                    $urg->parent_id = $s->parent_id;
                }
                if(isset($s->other_emts)) {
                    $urg->other_emts = $s->other_emts;
                }

		if(isset($s->comment)) {
		    $urg->comment = $s->comment;
		}
                $urg->created_by = Auth::user()->id;
                $urg->save();
                if($s->parent_id == null || $s->parent_id == ""){
                    $urg->parent_id = $urg->id;
                }
		$urg->save();
		
		//Setup materials
		if(isset($s->material_2)) {
			$s->material_2 = (array) $s->material_2;
			foreach($s->material_2 as $mat) {
				$l = new MissionMaterial;
				$l->mission_id = $urg->id;
				$l->material_id = $mat;
				$l->save();
			}
		}

		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Add Emergency')->first()->id;
		$event->save();
                $urgs[] = $urg;
                $this->setup_parties($s,$urg);

		SendEmail::dispatch($urg)->onQueue('app_1');

        }catch(\Exception $e){
Log::warning($e);
            foreach ($urgs as $ur) {
                $ur->delete();
            }
            $response = New Response;
            $response->Status = 0;
            Log::warning($e->getMessage());
            return json_encode($response);
        }
        $response = New Response;
        $response->Status = 1;
        return json_encode($response);
        
    }

}
