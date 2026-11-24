<?php

namespace App\Http\Controllers;
/*
use Request;
use DateTime;
use app\Http\Requests;
use Auth;

use App\Http\ORM\emergency_medical_provider;
use App\Http\ORM\ambulance;
use App\Http\ORM\mission_type;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\material as Material;
use App\Http\ORM\hospital;
use App\Http\ORM\location;
use App\Http\ORM\mission;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\MissionEMT;
use App\Http\ORM\Patient;
use App\Http\ORM\PatientMission;
use App\Http\ORM\MaterialLog;

use App\Responses\Response;
*/
class add_emergency extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        date_default_timezone_set("Asia/Beirut");
    }
   /* public function store(){

        $request = json_decode(Request::get('form'));

        $car_id = $request->car_number;
        $driver_id = $request->driver;
        $mission_leader_id = $request->m_leader;
        $emts = $request->emt;
        $date = $request->mission_date;
        $dep_time = $date . " " . $request->dep_hour . ":" . $request->dep_min;
        $ret_time = $date . " " . $request->ret_hour . ":" . $request->ret_min;

	$sds = $request->patients[0]->mission_type;

        if((int)$request->dep_hour > (int)$request->ret_hour){
            $ret_time = DateTime::createFromFormat('m/d/Y H:i',$ret_time)->modify("+1 day")->format('m/d/Y H:i');
        }

        //Create Mission Log
        $missionLog = New mission;
	if($sds != 13)
	        $missionLog->ambulance = $car_id;
        $missionLog->departure_time = DateTime::createFromFormat('m/d/Y H:i',$dep_time);
        $missionLog->return_time = DateTime::createFromFormat('m/d/Y H:i',$ret_time);
        $missionLog->save();

	
        //Set Driver
	if($sds != 13){
		$missionDriverLog = New MissionDriver;
        	$missionDriverLog->mission_id = $missionLog->id;
        	$missionDriverLog->driver_id = $driver_id;
        	$missionDriverLog->save();
	}
	
        //Set Mission Leader
        $missionLeaderLog = New MissionLeader;
        $missionLeaderLog->mission_id = $missionLog->id;
        $missionLeaderLog->mission_leader_id = $mission_leader_id;
        $missionLeaderLog->save();

        foreach ($emts as $emt) {
            $missionEmtLog = New MissionEMT;
            $missionEmtLog->mission_id = $missionLog->id;
            $missionEmtLog->emt_id = $emt;
            $missionEmtLog->save();
        }

        foreach ($request->patients as $patient) {
            $first_name = $patient->first_name;
            $last_name = $patient->last_name;
            $year_of_birth = $patient->year;
	    $annuler = $patient->annuler;

		
            $patientt = Patient::where('first_name',$first_name)
                            ->where('last_name',$last_name)
                            ->where('date_of_birth',$year_of_birth)->first();

            if(is_null($patientt)){
                $patientt = New Patient;
                $patientt->first_name = $first_name;
                $patientt->last_name = $last_name;
                $patientt->date_of_birth = $year_of_birth;
                $patientt->save();
            }

            $patientMissionLog = New PatientMission;
            $patientMissionLog->emergency_id = $missionLog->id;
            $patientMissionLog->patient_id = $patientt->id;

            switch ($patient->from) {
                case '0':
                    $patientMissionLog->from_hospital = 1;
                    $patientMissionLog->hospital_from = $patient->from_specific;
                    break;

                case '1':
                    $patientMissionLog->from_house = 1;
                    $patientMissionLog->house_from = $patient->from_specific;
                    break;

                case '2':
                    $patientMissionLog->from_road = 1;
                    $patientMissionLog->road_from = $patient->from_specific;
                    break;

                case '3':
                    $patientMissionLog->from_station = 1;
                    $patientMissionLog->station_from = $patient->from_specific;
                    break;

                case '4':
                    $patientMissionLog->from_other = 1;
                    $patientMissionLog->other_from = $patient->from_specific;
                    break;
            }

            switch ($patient->to) {
                case '0':
                    $patientMissionLog->to_hospital = 1;
                    $patientMissionLog->hospital_to = $patient->to_specific;
                    break;

                case '1':
                    $patientMissionLog->to_house = 1;
                    $patientMissionLog->house_to = $patient->to_specific;
                    break;

                case '2':
                    $patientMissionLog->to_road = 1;
                    $patientMissionLog->road_to = $patient->to_specific;
                    break;

                case '3':
                    $patientMissionLog->to_station = 1;
                    $patientMissionLog->station_to = $patient->to_specific;
                    break;

                case '4':
                    $patientMissionLog->to_other = 1;
                    $patientMissionLog->other_to = $patient->to_specific;
                    break;
            }

            $patientMissionLog->chief_complaint = $patient->complaint;
            $patientMissionLog->mission_type_id = $patient->mission_type;
            $patientMissionLog->save();

            $material = explode(';',$patient->material);

            if(!empty($material)){
                foreach($material as $mat){
                    if(trim($mat) != ""){
                        $materialLog = New MaterialLog;
                        $mat = Material::where('name',trim($mat))->first();
                        $materialLog->material_id = $mat == null ? null : $mat->id;
                        $materialLog->mission_patient_id = $patientMissionLog->id;
                        $materialLog->save();
                    }
                }
            }


        }
        $response = New Response;
        $response->Status = 1;
        return json_encode($response);*/
    }

}
