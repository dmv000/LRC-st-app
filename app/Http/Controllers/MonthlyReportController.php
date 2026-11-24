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
use App\Http\ORM\location as Village;
use App\Http\ORM\station;
use App\Http\ORM\Comment as comment;
use App\Http\ORM\Urgence;
use App\Http\ORM\MissionEMT;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;

use Illuminate\Support\Facades\Log;
class MonthlyReportController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {


        $date = Request::get('Month');
        $report = array();

        if($date == ""){
            $date = Date("Y-m");
            $date = DateTime::createFromFormat('Y-m-d',$date)->modify('-1 month')->format('Y-m');
        }

        $date = DateTime::createFromFormat('Y-m-d',$date);

        if(!is_object($date)){
            $date = Date("Y-m-d");
            $date = DateTime::createFromFormat('Y-m-d',$date)->modify('-1 day')->format('Y-m-d');
        }

        $emergencies = Urgence::select('dep_to_site',
                                        'ret_to_centre',
                                        'ambulance_id',
                                        'id',
                                        'parent_id',
                                        'patient_name',
                                        'patient_age',
                                        'patient_case',
                                        'type',
                                        'material_used',
                                        'from',
                                        'from_other',
                                        'to',
                                        'to_other',
                                        'o2',
                                        'other_emts'
                                    )
                                ->where('call_time','>=',$date->format('Y-m-d'))
                                ->where('call_time','<=',$date->modify('+1 day')->format('Y-m-d'))
				->where('is_deleted',0)
                                ->orderBy('dep_to_site')
                               // ->orderBy('created_at')
                                ->get()
                                ->groupBy('parent_id');

        $date = $date->modify('-1 day');

        $types = \DB::select("SELECT mission_type.name,mission_type.id as id,count( distinct(urgence.parent_id) ) as count
                            FROM urgence
                            LEFT JOIN mission_type
                            ON urgence.type = mission_type.id
                            WHERE urgence.call_time >= '" . 
                            $date->format('Y-m-d') . "'
                            AND urgence.call_time <= '" .
                            $date->modify('+1 day')->format('Y-m-d') . "' 
			    AND urgence.is_deleted = 0 
                            GROUP BY urgence.type
                            ORDER BY mission_type.id ASC");
        $casualties = false;
        $date = $date->modify('-1 day');
        foreach ($types as $type) {

            if($type->id == 1){
                //3adad isabet bl hades

                $tt = array();
                $tt['name'] = $type->name;
                $tt['count'] = $type->count;
                $tt['casualties'] = \DB::select("Select count(*) as count from urgence 
                                            where urgence.call_time >= '" . $date->format('Y-m-d') . "'
                                            AND urgence.call_time <= '" . $date->modify('+1 day')->format('Y-m-d') . "' and urgence.to like '0.%' and urgence.type = 1")[0]->count;
                $date = $date->modify('-1 day');
                $report[] = $tt;
                $casualties = true;

            }else{
                $tt = array();
                $tt['name'] = $type->name;
                $tt['count'] = $type->count;
                $report[] = $tt;
            }

           
        }
        if(count($report) == 0){
            $tt = array();
            $tt['name'] = '- -';
            $tt['count'] = '- -';
            $report[] =$tt;
        }



        $tableData = [];
        $count = 1;

        foreach ($emergencies as $key) {

            $count2 = 1;
	    
            foreach ($key as $k) {
                $record = [];   
		$record[] = $k->id;
                if(count($key) > 1){
                    $record[] = $count . "." . $count2;
                    $count2 += 1;
                }else{
                    $record[] = $count;
                }
            
            $record[] = DateTime::createFromFormat('Y-m-d H:i:s',$k->dep_to_site)->format('H:i');
            $record[] = DateTime::createFromFormat('Y-m-d H:i:s',$k->ret_to_centre)->format('H:i');

            $amb = ambulance::select('car_number')->where('id',$k->ambulance_id)->first();
            $record[] = $amb == null ? "-" : $amb->car_number;

            //dRIVER m leader emt
            $names_ar = array();

            $driver = $k->driver()->first();
            if(!is_null($driver)){
                $names_ar[] = EMT::select('name','nickname')->where('id',$driver->driver_id)->first();
            }

            $leader = $k->leader()->first();

            if(!is_null($leader)){
                $names_ar[] = EMT::select('name','nickname')->where('id',$leader->mission_leader_id)->first();
            }

            $emts = $k->emts()->get();
            if(!is_null($emts) && count($emts) > 0){
                foreach ($emts as $emt) {
                    error_log($emt->id);
                    $names_ar[] = EMT::select('name','nickname')->where('id',$emt->emt_id)->first();
                }
            }

            if(!is_null($k->other_emts) && $k->other_emts != ""){
                $o = new \stdClass();
                $o->nickname = $k->other_emts;
                $names_ar[] = $o;
            }

            $names = "";
            foreach ($names_ar as $name) {
                if($names != "") {
                    $names .= " & ";
                }
                if($name->nickname == ""){
                    $names .= $name->name;
                }else{
                    $names .= $name->nickname;
                }
            }

            $record[] = $names;
            $record[] = $k->patient_name == "" ? "- -" : str_replace("%%% ", "", $k->patient_name);
            $record[] = $k->patient_age == "" ? "- -" : (date("Y") - $k->patient_age . " (" . $k->patient_age . ")");
            $record[] = $k->patient_case == "" ? "- -" : $k->patient_case;
	    $record[] = mission_type::where('id',$k->type)->first()->name;
            //from
            $from = $k->from;
            if($from == "4"){
                $record[] = $k->from_other;

            }else{
                //hospital
                $tt = explode(".", $from);
                if($tt[0] == 0){
                    $h = Hospital::where('id',$tt[1])->first();
                    $record[] = $h->name . " (" . $h->code . ")"; 
                }else if($tt[0] == 1){
                    $h = Village::where('id',$tt[1])->first();
                    $record[] = "Δ " . $h->village . " (" . $h->station . ")";  
                }else if($tt[0] == 2){
                    $h = Village::where('id',$tt[1])->first();
                    $record[] = "عريض " . $h->village . " (" . $h->station . ")";  
                }else if($tt[0] == 3){
                    $c = station::where('id',$tt[1])->first();
                    $record[] = "C" . $c->id . " (" . $c->name . ")";  
                }         
            }

            //to
            $to = $k->to;
            if($to == "4"){
                $record[] = $k->to_other;

            }else{
                //hospital
                $tt = explode(".", $to);
                if($tt[0] == 0){
                    $h = Hospital::where('id',$tt[1])->first();
                    $record[] = $h->name . " (" . $h->code . ")"; 
                }else if($tt[0] == 1){
                    $h = Village::where('id',$tt[1])->first();
                    $record[] = "Δ " . $h->village . " (" . $h->station . ")";  
                }else if($tt[0] == 2){
                    $h = Village::where('id',$tt[1])->first();
                    $record[] = "عريض " . $h->village . " (" . $h->station . ")";  
                }else if($tt[0] == 3){
                    $c = station::where('id',$tt[1])->first();
                    $record[] = "C" . $c->id . " (" . $c->name . ")";  
                }         
            }


            $record[] = $k->o2;

            $tableData[] = $record;
        }
        $count += 1;
        }
        $count = count($tableData);
        $date = $date->format('Y-m-d');
        $comm = comment::where('date',$date)->first();
        
        $from = "";
        $to = "";
        
        if(!is_null($comm)){
            $from = is_null($comm->from) || $comm->from == "" ? "" : EMT::select('name','nickname')->where('id',$comm->from)->first();
            if(!is_null($from) && $from != ""){
                if($from->nickname == ""){
                    $from = $from->name;
                }else{
                    $from = $from->name . " (" . $from->nickname . ")";
                }
            }
            $to = is_null($comm->to) ? "" : $comm->to;
        }
        $comm = is_null($comm) ? "" : $comm->content;

        $emts = EMT::select('name','nickname','id')->where('is_deleted',0)->orderBy('name','asc')->get();

        foreach ($emts as $emt) {
            if($emt->nickname == ""){
                $emt->nickname = $emt->name;
            }else{
                $emt->nickname = $emt->name . " (" . $emt->nickname . ")";
            }
        }

      //  $report = str_replace(array("\n","\r"),"9999",$report);
        $comm = str_replace(array("\n","\r"),"9999",$comm);

        return view('report',compact('tableData','count','comm','date','emts', 'from', 'to', 'report', 'casualties'));
    }
}
