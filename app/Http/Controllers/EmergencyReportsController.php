<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;
use Excel;

use App\Responses\Response;
use App\User;
use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\MissionEMT;
use App\Http\ORM\Urgence;
use App\Http\ORM\mission_type as MissionType;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

class EmergencyReportsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    private function getOrder($user){

	if($user->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id))
		return 0;
	if($user->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Chef Equipe')->first()->id))
		return 1;
	if($user->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Driver')->first()->id))
		return 2;
	return 3;

    }
	
	public function createWeeklyReport() {
		
		if(!Auth::check()) return "Unauthorized access. Please Login";
		$access = Auth::user()->memberOf('Statistic');
		if(!$access) return "Unauthorized Access";
		
		$year = Input::get('year');
        $month = Input::get('month');
		if($year == "" || $year == -1 || $month == "" || $month == -1) {
			return;
		}

		$firstDay = 1;
		$lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		$firstDate = new DateTime( $year . "-" . $month . "-" . $firstDay );
		$lastDate = new DateTime( $year . "-" . $month . "-" . $lastDay );  
		
		$name = strftime("%A", strtotime($firstDate->format("Y-m-d")) );
		
		if( $name == "Sunday" ) {
			$firstDate = $firstDate->modify("next monday");
		}
		
		$name = strftime("%A", strtotime($lastDate->format("Y-m-d")) );
		
		if( $name == "Sunday" ) {
			$lastDate = $lastDate->modify("last saturday");
		}		

		$firstWeek = $firstDate->format("W");
		$lastWeek = $lastDate->format("W");
		
		//Log::warning($firstDate->format("Y-m-d") . " " . $lastDate->format("Y-m-d"));		
		//Log::warning($firstWeek . " " . $lastWeek . " " . $lastDate->format("Y-m-d"));
		
		$numberWeeks = $lastWeek - $firstWeek;
		
		if($numberWeeks < 0) {
			$numberWeeks = $numberWeeks + 52;//Last Week of the year
		}
		
		$results = array();
		
		$currentDay = $firstDay;
		
		$types = array();
		
		$types[] = 1;
		
		$types[] = 2;
		$types[] = 3;
		$types[] = 4;
		$types[] = 5;
		$types[] = 6;
		$types[] = 7;
		$types[] = 8;
		$types[] = 9;
		$types[] = 10;
		$types[] = 12;
		$types[] = 13;
		$types[] = 14;
		$types[] = 15;
		$types[] = 16;
		$types[] = 17;
		$types[] = 18;
		$types[] = 21;
		
		
		for( $counter = 0 ; $counter <= $numberWeeks ; $counter++ ) {
			$results[] = array();
			$results[$counter]["counts"] = array();
			for( $c=0;$c<count($types);$c++ ) {
				$results[$counter]["counts"][ $types[$c] ] = 0;
			}
		}
		
		Log::warning($results);
		
		for($day = $firstDate->format("d"); $day <= $lastDate->format("d"); $day++) { 
		
			$name = strftime("%A", strtotime(date($year . "-" . $month . "-" . $day) ) );
		
			if( $name == "Sunday" ) {
				continue;
			}
			
			if( $name == "Saturday" ) {
				$fromDate = "07:00:00";
				$toDate   = "15:00:00";
			} else {
				$fromDate = "07:00:00";
				$toDate   = "18:00:00";
			}
			
			$dailyCount = Urgence::select('type' , \DB::raw('count(distinct parent_id) as total'))->
			whereBetween('dep_to_site', [ $year . "-" . $month . "-" . $day . ' ' . $fromDate , $year . "-" . $month . "-" . $day . ' ' . $toDate ])->where('is_deleted',0)->groupBy('type')->get();
		
			$currentDate = new DateTime( $year . "-" . $month . "-" . $day );
			$weekNumber = $currentDate->format("W") - $firstWeek;
			
			if($weekNumber < 0) {
				
				$weekNumber = $weekNumber + 52;
			}
			
			if( !isset( $results[$weekNumber]["start"] ) ) {
				$results[$weekNumber]["start"] = $currentDate->format("Y-m-d");
			}
			
			foreach( $dailyCount as $singleRes ) {
				if( isset( $results[$weekNumber]["counts"][$singleRes->type] ) ) {
					$results[$weekNumber]["counts"][$singleRes->type] = $results[$weekNumber]["counts"][$singleRes->type] + $singleRes->total;
				}					
			}
			
			$results[$weekNumber]["end"] = $currentDate->format("Y-m-d");
			
		}
		
		$typeNames = array();
		foreach( $types as $t ) {
			$typeNames [$t] = MissionType::where('id',$t)->first()->name;
		}
		
		/*$returnStr = "Week";
		foreach( $types as $t ) {
			$returnStr = $returnStr . " , " . $typeNames[$t];
		}
		$returnStr = $returnStr . "\n";
		
		foreach( $results as $week ) {
			$returnStr .= "From : " . $week["start"] . " to " . $week["end"];
			foreach( $types as $t ) {
				$returnStr = $returnStr . " , " . $week["counts"][$t];
			}
			$returnStr = $returnStr . "\n";
		}
		return $returnStr;*/
		
		Excel::create('Day_Missions_' . $month . "_" . $year, function($excel) use ($results,$typeNames,$types) {
            		
			$excel->setTitle('Day Missions Report') 
				->setCreator('IT')
                ->setCompany('LRC IT')
				->setDescription('!!');

            		$excel->sheet('Report', function($sheet) use ($results,$typeNames,$types) {

					$top = array();
					$top[] = "Week";
					foreach( $types as $t ) {
						$top[] = $typeNames[$t];
					}					
					$top[] = "Total";
					
					$sheet->row(1, $top);

					$sheet->row(1, function($row) {

						// call cell manipulation methods
						$row->setBackground('#939FA3');
						$row->setAlignment('center');
						$row->setVAlignment('center');
						$row->setFontSize(14);
						$row->setFontWeight('bold');
					});
					
					$rowCount = count($results)+2;

					$sheet->freezeFirstRow();
					
                		$sheet->cells('A2:T' . $rowCount , function($cells) {

                    			$cells->setAlignment('left');
                    			$cells->setVAlignment('top');
                    			$cells->setFontSize(12);

                		});
                		$sheet->setAutoSize(true);
					$sheet->setBorder("A1:T".$rowCount, 'thick');

				$pos = 2;
				
				$totalRow = array();
				$totalRow[] = "Total";
				foreach( $types as $t ) {
					$totalRow[] = 0;
				}
				$totalRow[] = 0;
				
				foreach( $results as $week ) {
					
					Log::warning($week);
					
					$row = array();
					//$row[] = "From : " . $week["start"] . " to " . $week["end"];
					$row[] = "Week #" . ($pos-1);
					$total = 0;
					$index = 1;
					foreach( $types as $t ) {
						$row[] = $week["counts"][$t];
						$totalRow[$index] = $totalRow[$index] + $week["counts"][$t];
						$total = $total + $week["counts"][$t];
						$index = $index + 1;
					}
					$row[] = $total;
					$totalRow[$index] = $totalRow[$index] + $total;
					
					$sheet->row($pos++ , $row);
				}
				$sheet->row($pos , $totalRow);

				
			});

		})->download('xls');
		
		/*echo "<table border='1' cellspacing='0' cellpadding='4'><th>";
		foreach( $types as $t ) {
			echo "<td>" . $typeNames[$t] . "</td>";
		}echo "<th/>";
		
		foreach( $results as $week ) {
				echo "<tr><td>From : " . $week["start"] . " to " . $week["end"];
				foreach( $types as $t ) {
					echo "<td>" . $week["counts"][$t] . "</td>";
				}echo "<tr/>";
		}
		echo "</table>";*/
		
		//return $results;
		
	}

	public function createEmtCountReport() {

	if(!Auth::check()) return "Unauthorized access. Please Login";
	
	//$secretariat_admin = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Secretariat Admin')->first()->id);
	//$head_of_station = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id);
	$access = Auth::user()->memberOf('Statistic');

	//if(!$secretariat_admin && !$head_of_station) return "Unauthorized access";
	if(!$access) return "Unauthorized Access";
        $year = Input::get('year');
        $month = Input::get('month');
	if($year == "" || $year == -1 || $month == "" || $month == -1) {
		return;
	} 

//	$month = 9;
//	$year = 2018;
	$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//	$days = 11;
	//$drivers = User::where('driver',1)->where('is_deleted',0)->get();
	$drivers = User::where('driver',1)->get();
	//$users = User::where('is_deleted',0)->get();
	$users = User::get();

	$drivers_res_day = array();
	$drivers_res_night = array();
	$drivers_res = array();

	$mission_leaders_res = array();
	$mission_leaders_res_day = array();
	$mission_leaders_res_night = array();

	$emts_res = array();
	$emts_res_day = array();
	$emts_res_night = array();

	$all_res_day = array();
	$all_res_night = array();
	$all_res = array();

	$avp = array();
	$avp[] = 1;

	$urg = array();
	$urg[] = 2;
	$urg[] = 3;
	$urg[] = 4;
	$urg[] = 5;
	$urg[] = 6;
	$urg[] = 7;
	$urg[] = 8;
	$urg[] = 9;
	$urg[] = 10;
	$urg[] = 11;
	$urg[] = 17;
	$urg[] = 19;
	$urg[] = 20;

	$tra = array();
	$tra[] = 12; 

	foreach($drivers as $driver) {
		$drivers_res[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$driver->id)->first()->nickname ) , "All" => 0);
		$drivers_res_day[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$driver->id)->first()->nickname ) , "Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$drivers_res_night[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$driver->id)->first()->nickname ) , "Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
	}

	foreach($users as $user) {
		$mission_leaders_res[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) , "All" => 0);
		$mission_leaders_res_day[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) , "Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$mission_leaders_res_night[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);

		$emts_res[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"All" => 0);
		$emts_res_day[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$emts_res_night[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
		
		$all_res[$user->id] = 		array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"All" => 0);
		$all_res_day[$user->id] = 	array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$all_res_night[$user->id] = 	array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => str_replace( env('STATION_NUMBER') , '' , User::where('id',$user->id)->first()->nickname ) ,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
	}

	for($day = 1; $day <= $days; $day++) {
		$name = strftime("%A", strtotime(date($year . "-" . $month . "-" . $day)));
		if($name == "Saturday") {
			$dayStart = "07:00:00";
			$dayEnd = "18:00:00";
		}else if($name == "Sunday") {
			$dayStart = "00:00:00";
			$dayEnd = "00:00:00";
		}else{
			$dayStart = "07:00:00";
			$dayEnd = "18:00:00";
		}
		$logs = \DB::select("SELECT Us.name as Name
					,time(Ur.dep_to_site) as Time
					,Ur.type as Type
					,Ur.parent_id as pid
					,Us.id as id
					,1 as emt
				FROM mission_driver MD
				LEFT JOIN urgence Ur 
					on MD.mission_id = Ur.id
				LEFT JOIN users Us
					on MD.driver_id = Us.id
				WHERE Ur.dep_to_site <= '" . $year . "-" . $month . "-" . $day . " 23:59:59'
				AND Ur.dep_to_site >= '" . $year . "-" . $month . "-" . $day . " 00:00:00'
				AND Ur.is_deleted = 0
				UNION ALL
				SELECT Us.name as Name
					,time(Ur.dep_to_site) as Time
					,Ur.type as Type
					,Ur.parent_id as pid
					,Us.id as id
					,2 as emt
				FROM mission_mission_leader ML
				LEFT JOIN urgence Ur 
					on ML.mission_id = Ur.id
				LEFT JOIN users Us
					on ML.mission_leader_id = Us.id
				WHERE Ur.dep_to_site <= '" . $year . "-" . $month . "-" . $day . " 23:59:59'
				AND Ur.dep_to_site >= '" . $year . "-" . $month . "-" . $day . " 00:00:00'
				AND Ur.is_deleted = 0
				UNION ALL
				SELECT Us.name as Name
					,time(Ur.dep_to_site) as Time
					,Ur.type as Type
					,Ur.parent_id as pid
					,Us.id as id
					,3 as emt
				FROM mission_emts ME
				LEFT JOIN urgence Ur 
					on ME.mission_id = Ur.id
				LEFT JOIN users Us
					on ME.emt_id = Us.id
				WHERE Ur.dep_to_site <= '" . $year . "-" . $month . "-" . $day . " 23:59:59'
				AND Ur.dep_to_site >= '" . $year . "-" . $month . "-" . $day . " 00:00:00'
				AND Ur.is_deleted = 0");

//echo json_encode($logs);
//echo "<br><br><br>";
$processed = array();
		foreach($logs as $log) {
			$uid = $log->pid . "u" . $log->id;
			if(!in_array($uid,$processed)) {
				$processed[] = $uid;
				if(in_array($log->Type,$avp)) {

					if($log->emt == 1) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$drivers_res_night[$log->id]["Night_AVP"] += 1;
							$drivers_res_night[$log->id]["Night"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_AVP"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$drivers_res_day[$log->id]["Day_AVP"] += 1;
							$drivers_res_day[$log->id]["Day"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_AVP"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 2) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$mission_leaders_res_night[$log->id]["Night_AVP"] += 1;
							$mission_leaders_res_night[$log->id]["Night"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_AVP"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$mission_leaders_res_day[$log->id]["Day_AVP"] += 1;
							$mission_leaders_res_day[$log->id]["Day"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_AVP"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 3) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$emts_res_night[$log->id]["Night_AVP"] += 1;
							$emts_res_night[$log->id]["Night"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_AVP"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$emts_res_day[$log->id]["Day_AVP"] += 1;
							$emts_res_day[$log->id]["Day"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_AVP"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					} 
				}else if(in_array($log->Type,$urg)) {

					if($log->emt == 1) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$drivers_res_night[$log->id]["Night_Urg"] += 1;
							$drivers_res_night[$log->id]["Night"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Urg"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$drivers_res_day[$log->id]["Day_Urg"] += 1;
							$drivers_res_day[$log->id]["Day"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Urg"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 2) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$mission_leaders_res_night[$log->id]["Night_Urg"] += 1;
							$mission_leaders_res_night[$log->id]["Night"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Urg"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$mission_leaders_res_day[$log->id]["Day_Urg"] += 1;
							$mission_leaders_res_day[$log->id]["Day"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Urg"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 3) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$emts_res_night[$log->id]["Night_Urg"] += 1;
							$emts_res_night[$log->id]["Night"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Urg"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$emts_res_day[$log->id]["Day_Urg"] += 1;
							$emts_res_day[$log->id]["Day"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Urg"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					} 
				}else if(in_array($log->Type,$tra)) {

					if($log->emt == 1) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$drivers_res_night[$log->id]["Night_Transport"] += 1;
							$drivers_res_night[$log->id]["Night"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Transport"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$drivers_res_day[$log->id]["Day_Transport"] += 1;
							$drivers_res_day[$log->id]["Day"] += 1;
							$drivers_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Transport"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 2) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$mission_leaders_res_night[$log->id]["Night_Transport"] += 1;
							$mission_leaders_res_night[$log->id]["Night"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Transport"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$mission_leaders_res_day[$log->id]["Day_Transport"] += 1;
							$mission_leaders_res_day[$log->id]["Day"] += 1;
							$mission_leaders_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Transport"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					}else if($log->emt == 3) {

						if($log->Time < $dayStart || $log->Time > $dayEnd) {

							$emts_res_night[$log->id]["Night_Transport"] += 1;
							$emts_res_night[$log->id]["Night"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_night[$log->id]["Night_Transport"] += 1;
							$all_res_night[$log->id]["Night"] += 1;
							$all_res[$log->id]["All"] += 1;					
						} else {
	
							$emts_res_day[$log->id]["Day_Transport"] += 1;
							$emts_res_day[$log->id]["Day"] += 1;
							$emts_res[$log->id]["All"] += 1;

							$all_res_day[$log->id]["Day_Transport"] += 1;
							$all_res_day[$log->id]["Day"] += 1;
							$all_res[$log->id]["All"] += 1;	
						}
					} 
				}
			}	
		}
	}
	//********************************************* Start Sort Complete Report ***************************************//

	$count  = array_column($all_res, 'All');
	$order = array_column($all_res, 'Order');
	$id = array_column($all_res, 'Id');
	$name = array_column($all_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res);

	$count  = array_column($all_res_day, 'Day');
	$order = array_column($all_res_day, 'Order');
	$id = array_column($all_res_day, 'Id');
	$name = array_column($all_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res_day);

	$count  = array_column($all_res_night, 'Night');
	$order = array_column($all_res_night, 'Order');
	$id = array_column($all_res_night, 'Id');
	$name = array_column($all_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res_night);

	//********************************************* End Sort Complete Report *****************************************//
	//********************************************* Start Sort Dirvers Report ****************************************//

	$count  = array_column($drivers_res, 'All');
	$order = array_column($drivers_res, 'Order');
	$id = array_column($drivers_res, 'Id');
	$name = array_column($drivers_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res);

	$count  = array_column($drivers_res_day, 'Day');
	$order = array_column($drivers_res_day, 'Order');
	$id = array_column($drivers_res_day, 'Id');
	$name = array_column($drivers_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res_day);

	$count  = array_column($drivers_res_night, 'Night');
	$order = array_column($drivers_res_night, 'Order');
	$id = array_column($drivers_res_night, 'Id');
	$name = array_column($drivers_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res_night);

	//********************************************* End Sort Drivers Report ******************************************//
	//********************************************* Start Sort Mission Leaders Report ********************************//

	$count  = array_column($mission_leaders_res, 'All');
	$order = array_column($mission_leaders_res, 'Order');
	$id = array_column($mission_leaders_res, 'Id');
	$name = array_column($mission_leaders_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res);

	$count  = array_column($mission_leaders_res_day, 'Day');
	$order = array_column($mission_leaders_res_day, 'Order');
	$id = array_column($mission_leaders_res_day, 'Id');
	$name = array_column($mission_leaders_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res_day);

	$count  = array_column($mission_leaders_res_night, 'Night');
	$order = array_column($mission_leaders_res_night, 'Order');
	$id = array_column($mission_leaders_res_night, 'Id');
	$name = array_column($mission_leaders_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res_night);

	//********************************************* End Sort Mission Leaders Report **********************************//
	//********************************************* Start Sort EMTs Report *******************************************//

	$count  = array_column($emts_res, 'All');
	$order = array_column($emts_res, 'Order');
	$id = array_column($emts_res, 'Id');
	$name = array_column($emts_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res);

	$count  = array_column($emts_res_day, 'Day');
	$order = array_column($emts_res_day, 'Order');
	$id = array_column($emts_res_day, 'Id');
	$name = array_column($emts_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res_day);

	$count  = array_column($emts_res_night, 'Night');
	$order = array_column($emts_res_night, 'Order');
	$id = array_column($emts_res_night, 'Id');
	$name = array_column($emts_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res_night);

	//********************************************* End Sort EMTs Report ********************************************//
	//********************************************* Start All Report ************************************************//

	$all_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($all_res); $i++){
		
		if ( $all_res[$i]["All"] != 0 ) {
			$all_str .= $all_res_day[$i]["Name"] . "," . $all_res_day[$i]["Day_Urg"] . "," . $all_res_day[$i]["Day_AVP"] . "," 
					. $all_res_day[$i]["Day_Transport"] . "," . $all_res_day[$i]["Day"] . "," . ($i + 1) . "," . $all_res_night[$i]["Name"] . ","
					. $all_res_night[$i]["Night_Urg"] . "," . $all_res_night[$i]["Night_AVP"] . "," . $all_res_night[$i]["Night_Transport"] . ","
					. $all_res_night[$i]["Night"] . "," . ($i + 1) . "," . $all_res[$i]["Name"] . "," . $all_res[$i]["All"] . "," . ($i+1) . "\n";
		}
	}

	//********************************************* End All Report ***************************************************//
	//********************************************* Start Drivers Report *********************************************//

	$drivers_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($drivers_res); $i++){
		
		if ( $drivers_res[$i]["All"] != 0 ) {
			$drivers_str .= $drivers_res_day[$i]["Name"] . "," . $drivers_res_day[$i]["Day_Urg"] . "," . $drivers_res_day[$i]["Day_AVP"] . "," 
					. $drivers_res_day[$i]["Day_Transport"] . "," . $drivers_res_day[$i]["Day"] . "," . ($i + 1) . "," . $drivers_res_night[$i]["Name"] . ","
					. $drivers_res_night[$i]["Night_Urg"] . "," . $drivers_res_night[$i]["Night_AVP"] . "," . $drivers_res_night[$i]["Night_Transport"] . ","
					. $drivers_res_night[$i]["Night"] . "," . ($i + 1) . "," . $drivers_res[$i]["Name"] . "," . $drivers_res[$i]["All"] . "," . ($i+1) . "\n";
		}
	}

	//********************************************* End Drivers Report ***********************************************//
	//********************************************* Start Mission Leaders Report *************************************//

	$mission_leaders_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($mission_leaders_res); $i++){
		
		if( $mission_leaders_res[$i]["All"] != 0 ) {
			$mission_leaders_str .= $mission_leaders_res_day[$i]["Name"] . "," . $mission_leaders_res_day[$i]["Day_Urg"] . "," . $mission_leaders_res_day[$i]["Day_AVP"] . "," 
					. $mission_leaders_res_day[$i]["Day_Transport"] . "," . $mission_leaders_res_day[$i]["Day"] . "," . ($i + 1) . "," . $mission_leaders_res_night[$i]["Name"] . ","
					. $mission_leaders_res_night[$i]["Night_Urg"] . "," . $mission_leaders_res_night[$i]["Night_AVP"] . "," . $mission_leaders_res_night[$i]["Night_Transport"] . ","
					. $mission_leaders_res_night[$i]["Night"] . "," . ($i + 1) . "," . $mission_leaders_res[$i]["Name"] . "," . $mission_leaders_res[$i]["All"] . "," . ($i+1) . "\n";
		}
	}

	//********************************************* End Mission Leaders Report ***************************************//
	//********************************************* Start EMTs Report ************************************************//

	$emts_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($emts_res); $i++){
		
		if( $emts_res[$i]["All"] != 0 ) {
			$emts_str .= $emts_res_day[$i]["Name"] . "," . $emts_res_day[$i]["Day_Urg"] . "," . $emts_res_day[$i]["Day_AVP"] . "," 
					. $emts_res_day[$i]["Day_Transport"] . "," . $emts_res_day[$i]["Day"] . "," . ($i + 1) . "," . $emts_res_night[$i]["Name"] . ","
					. $emts_res_night[$i]["Night_Urg"] . "," . $emts_res_night[$i]["Night_AVP"] . "," . $emts_res_night[$i]["Night_Transport"] . ","
					. $emts_res_night[$i]["Night"] . "," . ($i + 1) . "," . $emts_res[$i]["Name"] . "," . $emts_res[$i]["All"] . "," . ($i+1) . "\n";
		}
	}

	//********************************************* End EMTs Report **************************************************//

	


	$time = time();
    \Zipper::make(storage_path('reports/Reports_' . $time . '.zip'))
			->addString('All.csv',$all_str)->addString('Drivers.csv',$drivers_str)
			->addString('Mission_leaders.csv',$mission_leaders_str)
			->addString('EMTs.csv',$emts_str)
			//->addString('MissionTypeCount.csv',$type_str)
			//->addString('VillageMissionCount.csv',$village_str)
			->close();
   
    return response()->download(storage_path('reports/Reports_' . $time . '.zip'));
	//??TODO
	//?? ADD OTHER REPORTS


	}
    public function driver_report() {

	$month = 9;
	$year = 2018;
//	$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$days = 1;

	$drivers = User::where('driver',1)->where('is_deleted',0)->get();
	$users = User::where('is_deleted',0)->get();

	$drivers_res_day = array();
	$drivers_res_night = array();
	$drivers_res = array();

	$mission_leaders_res = array();
	$mission_leaders_res_day = array();
	$mission_leaders_res_night = array();

	$emts_res = array();
	$emts_res_day = array();
	$emts_res_night = array();

	$all_res_day = array();
	$all_res_night = array();
	$all_res = array();

	foreach($drivers as $driver) {
		$drivers_res[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => User::where('id',$driver->id)->first()->nickname, "All" => 0);
		$drivers_res_day[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => User::where('id',$driver->id)->first()->nickname, "Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$drivers_res_night[$driver->id] = array("Id" => $driver->id, "Order" => $this->getOrder($driver), "Name" => User::where('id',$driver->id)->first()->nickname, "Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
	}

	foreach($users as $user) {
		$mission_leaders_res[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname, "All" => 0);
		$mission_leaders_res_day[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname, "Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$mission_leaders_res_night[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);

		$emts_res[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"All" => 0);
		$emts_res_day[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$emts_res_night[$user->id] = array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
		
		$all_res[$user->id] = 		array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"All" => 0);
		$all_res_day[$user->id] = 	array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"Day_Urg" => 0,"Day_AVP" => 0,"Day_Transport" => 0,"Day" => 0);
		$all_res_night[$user->id] = 	array("Id" => $user->id, "Order" => $this->getOrder($user), "Name" => User::where('id',$user->id)->first()->nickname,"Night_Urg" => 0,"Night_AVP" => 0,"Night_Transport" => 0, "Night" => 0);
	}

	for($day = 1; $day <= $days; $day++) {
		$name = strftime("%A", strtotime(date($year . "-" . $month . "-" . $day)));
		if($name == "Saturday") {
			$dayStart = "07:00:00";
			$dayEnd = "15:00:00";
		}else if($name == "Sunday") {
			$dayStart = "00:00:00";
			$dayEnd = "00:00:00";
		}else{
			$dayStart = "07:00:00";
			$dayEnd = "18:00:00";
		}
		
		//************************************* Start Day Shift Urgence *******************************************//
		//////********************************* Start Day Shift Drivers Report ********************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->whereNotIn('type', array(1,12,13,14))
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_day[$log->id]["Day_Urg"] += $log->count;
			$drivers_res_day[$log->id]["Day"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Urg"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift Drivers Report **********************************//////
		//////********************************* Start Day Shift Mission Leaders Report ************************//////

		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_day[$log->id]["Day_AVP"] += $log->count;
			$mission_leaders_res_day[$log->id]["Day"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Urg"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift Mission Leaders Report **************************//////
		//////********************************* Start Day Shift EMTs Report ***********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->whereNotIn('type', array(1,12,13,14))
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_day[$log->id]["Day_Urg"] += $log->count;
			$emts_res_day[$log->id]["Day"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Urg"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift EMTs Report *************************************//////
		//************************************* End Day Shift Urgence *********************************************//
		//************************************* Start Day Shift AVP ***********************************************//
		//////********************************* Start Day Shift Drivers Report ********************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_day[$log->id]["Day_AVP"] += $log->count;
			$drivers_res_day[$log->id]["Day"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_AVP"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift Drivers Report **********************************//////
		//////********************************* Start Day Shift Mission Leaders Report ************************//////

		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_day[$log->id]["Day_AVP"] += $log->count;
			$mission_leaders_res_day[$log->id]["Day"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_AVP"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift Mission Leaders Report **************************//////
		//////********************************* Start Day Shift EMTs Report ***********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_day[$log->id]["Day_AVP"] += $log->count;
			$emts_res_day[$log->id]["Day"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_AVP"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift EMTs Report *************************************//////
		//************************************* End Day Shift AVP *************************************************//
		//************************************* Start Day Shift Transport *****************************************//
		//////********************************* Start Day Shift Drivers Report ********************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_day[$log->id]["Day_Transport"] += $log->count;
			$drivers_res_day[$log->id]["Day"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Transport"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}
		
		//////********************************* End Day Shift Drivers Report **********************************//////
		//////********************************* Start Day Shift Mission Leaders Report ************************//////

		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_day[$log->id]["Day_Transport"] += $log->count;
			$mission_leaders_res_day[$log->id]["Day"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Transport"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift Mission Leaders Report **************************//////
		//////********************************* Start Day Shift EMTs Report ***********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_day[$log->id]["Day_Transport"] += $log->count;
			$emts_res_day[$log->id]["Day"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_day[$log->id]["Day_Transport"] += $log->count;
			$all_res_day[$log->id]["Day"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Day Shift EMTs Report *************************************//////
		//************************************* End Day Shift AVP *************************************************//
		//************************************* Start Night Shift Urgence *****************************************//
		//////********************************* Start Night Shift Drivers Report ******************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->whereNotIn('type', array(1,12,13,14))
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_night[$log->id]["Night_Urg"] += $log->count;
			$drivers_res_night[$log->id]["Night"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Urg"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Drivers Report ********************************//////
		//////********************************* Start Night Shift Mission Leaders Report **********************//////
		
		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->whereNotIn('type', array(1,12,13,14))
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_night[$log->id]["Night_Urg"] += $log->count;
			$mission_leaders_res_night[$log->id]["Night"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Urg"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Mission Leaders Report ************************//////
		//////********************************* Start Night Shift EMTs Report *********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->whereNotIn('type', array(1,12,13,14))
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_night[$log->id]["Night_Urg"] += $log->count;
			$emts_res_night[$log->id]["Night"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Urg"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift EMTs Report ***********************************//////
		//************************************* End Night Shift Urgence *******************************************//
		//************************************* Start Night Shift AVP *********************************************//
		//////********************************* Start Night Shift Drivers Report ******************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_night[$log->id]["Night_AVP"] += $log->count;
			$drivers_res_night[$log->id]["Night"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_AVP"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Drivers Report ********************************//////
		//////********************************* Start Night Shift Mission Leaders Report **********************//////

		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_night[$log->id]["Night_AVP"] += $log->count;
			$mission_leaders_res_night[$log->id]["Night"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_AVP"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Mission Leaders Report ************************//////
		//////********************************* Start Night Shift EMTs Report *********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 1)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0);
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_night[$log->id]["Night_AVP"] += $log->count;
			$emts_res_night[$log->id]["Night"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_AVP"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift EMTs Report ***********************************//////
		//************************************* End Night Shift AVP ***********************************************//
		//************************************* Start Night Shift Transport ***************************************//
		//////********************************* Start Night Shift Drivers Report ******************************//////

		$logs = MissionDriver::select(\DB::raw("count('driver_id') as count"),'driver_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0)
									->groupBy('parent_id');
							})->groupBy('driver_id')->get();
		foreach($logs as $log) {
			$drivers_res_night[$log->id]["Night_Transport"] += $log->count;
			$drivers_res_night[$log->id]["Night"] += $log->count;
			$drivers_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Transport"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Drivers Report ********************************//////
		//////********************************* Start Night Shift Mission Leaders Report **********************//////

		$logs = MissionLeader::select(\DB::raw("count('mission_leader_id') as count"),'mission_leader_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0)
									->groupBy('parent_id');
							})->groupBy('mission_leader_id')->get();
		foreach($logs as $log) {
			$mission_leaders_res_night[$log->id]["Night_Transport"] += $log->count;
			$mission_leaders_res_night[$log->id]["Night"] += $log->count;
			$mission_leaders_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Transport"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift Mission Leaders Report ************************//////
		//////********************************* Start Night Shift EMTs Report *********************************//////

		$logs = MissionEMT::select(\DB::raw("count('emt_id') as count"),'emt_id as id')
							->whereIn('mission_id',function($query) use($year,$month,$day,$dayStart,$dayEnd){
               							$query->select('parent_id')->distinct()->from('urgence')
									->where('type', 12)
									->whereDate('dep_to_site',$year . "-" . $month . "-" . $day)
									->whereNotBetween('dep_to_site',array($year . "-" . $month . "-" . $day . " " . $dayStart, $year . "-" . $month . "-" . $day . " " . $dayEnd))
									->where('is_deleted',0)
									->groupBy('parent_id');
							})->groupBy('emt_id')->get();
		foreach($logs as $log) {
			$emts_res_night[$log->id]["Night_Transport"] += $log->count;
			$emts_res_night[$log->id]["Night"] += $log->count;
			$emts_res[$log->id]["All"] += $log->count;

			$all_res_night[$log->id]["Night_Transport"] += $log->count;
			$all_res_night[$log->id]["Night"] += $log->count;
			$all_res[$log->id]["All"] += $log->count;
		}

		//////********************************* End Night Shift EMTs Report ***********************************//////
		//************************************* End Night Shift Transport *****************************************//
	}

	//********************************************* Start Sort Complete Report ***************************************//

	$count  = array_column($all_res, 'All');
	$order = array_column($all_res, 'Order');
	$id = array_column($all_res, 'Id');
	$name = array_column($all_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res);

	$count  = array_column($all_res_day, 'Day');
	$order = array_column($all_res_day, 'Order');
	$id = array_column($all_res_day, 'Id');
	$name = array_column($all_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res_day);

	$count  = array_column($all_res_night, 'Night');
	$order = array_column($all_res_night, 'Order');
	$id = array_column($all_res_night, 'Id');
	$name = array_column($all_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $all_res_night);

	//********************************************* End Sort Complete Report *****************************************//
	//********************************************* Start Sort Dirvers Report ****************************************//

	$count  = array_column($drivers_res, 'All');
	$order = array_column($drivers_res, 'Order');
	$id = array_column($drivers_res, 'Id');
	$name = array_column($drivers_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res);

	$count  = array_column($drivers_res_day, 'Day');
	$order = array_column($drivers_res_day, 'Order');
	$id = array_column($drivers_res_day, 'Id');
	$name = array_column($drivers_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res_day);

	$count  = array_column($drivers_res_night, 'Night');
	$order = array_column($drivers_res_night, 'Order');
	$id = array_column($drivers_res_night, 'Id');
	$name = array_column($drivers_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $drivers_res_night);

	//********************************************* End Sort Drivers Report ******************************************//
	//********************************************* Start Sort Mission Leaders Report ********************************//

	$count  = array_column($mission_leaders_res, 'All');
	$order = array_column($mission_leaders_res, 'Order');
	$id = array_column($mission_leaders_res, 'Id');
	$name = array_column($mission_leaders_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res);

	$count  = array_column($mission_leaders_res_day, 'Day');
	$order = array_column($mission_leaders_res_day, 'Order');
	$id = array_column($mission_leaders_res_day, 'Id');
	$name = array_column($mission_leaders_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res_day);

	$count  = array_column($mission_leaders_res_night, 'Night');
	$order = array_column($mission_leaders_res_night, 'Order');
	$id = array_column($mission_leaders_res_night, 'Id');
	$name = array_column($mission_leaders_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $mission_leaders_res_night);

	//********************************************* End Sort Mission Leaders Report **********************************//
	//********************************************* Start Sort EMTs Report *******************************************//

	$count  = array_column($emts_res, 'All');
	$order = array_column($emts_res, 'Order');
	$id = array_column($emts_res, 'Id');
	$name = array_column($emts_res, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res);

	$count  = array_column($emts_res_day, 'Day');
	$order = array_column($emts_res_day, 'Order');
	$id = array_column($emts_res_day, 'Id');
	$name = array_column($emts_res_day, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res_day);

	$count  = array_column($emts_res_night, 'Night');
	$order = array_column($emts_res_night, 'Order');
	$id = array_column($emts_res_night, 'Id');
	$name = array_column($emts_res_night, 'Name');

	array_multisort($count, SORT_DESC, $order, SORT_ASC, $id, SORT_ASC, $name, SORT_ASC, $emts_res_night);

	//********************************************* End Sort EMTs Report ********************************************//
	//********************************************* Start All Report ************************************************//

	$all_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($all_res); $i++){
		$all_str .= $all_res_day[$i]["Name"] . "," . $all_res_day[$i]["Day_Urg"] . "," . $all_res_day[$i]["Day_AVP"] . "," 
				. $all_res_day[$i]["Day_Transport"] . "," . $all_res_day[$i]["Day"] . "," . ($i + 1) . "," . $all_res_night[$i]["Name"] . ","
				. $all_res_night[$i]["Night_Urg"] . "," . $all_res_night[$i]["Night_AVP"] . "," . $all_res_night[$i]["Night_Transport"] . ","
				. $all_res_night[$i]["Night"] . "," . ($i + 1) . "," . $all_res[$i]["Name"] . "," . $all_res[$i]["All"] . "," . ($i+1) . "\n";

	}

	//********************************************* End All Report ***************************************************//
	//********************************************* Start Drivers Report *********************************************//

	$drivers_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($drivers_res); $i++){
		$drivers_str .= $drivers_res_day[$i]["Name"] . "," . $drivers_res_day[$i]["Day_Urg"] . "," . $drivers_res_day[$i]["Day_AVP"] . "," 
				. $drivers_res_day[$i]["Day_Transport"] . "," . $drivers_res_day[$i]["Day"] . "," . ($i + 1) . "," . $drivers_res_night[$i]["Name"] . ","
				. $drivers_res_night[$i]["Night_Urg"] . "," . $drivers_res_night[$i]["Night_AVP"] . "," . $drivers_res_night[$i]["Night_Transport"] . ","
				. $drivers_res_night[$i]["Night"] . "," . ($i + 1) . "," . $drivers_res[$i]["Name"] . "," . $drivers_res[$i]["All"] . "," . ($i+1) . "\n";

	}

	//********************************************* End Drivers Report ***********************************************//
	//********************************************* Start Mission Leaders Report *************************************//

	$mission_leaders_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($mission_leaders_res); $i++){
		$mission_leaders_str .= $mission_leaders_res_day[$i]["Name"] . "," . $mission_leaders_res_day[$i]["Day_Urg"] . "," . $mission_leaders_res_day[$i]["Day_AVP"] . "," 
				. $mission_leaders_res_day[$i]["Day_Transport"] . "," . $mission_leaders_res_day[$i]["Day"] . "," . ($i + 1) . "," . $mission_leaders_res_night[$i]["Name"] . ","
				. $mission_leaders_res_night[$i]["Night_Urg"] . "," . $mission_leaders_res_night[$i]["Night_AVP"] . "," . $mission_leaders_res_night[$i]["Night_Transport"] . ","
				. $mission_leaders_res_night[$i]["Night"] . "," . ($i + 1) . "," . $mission_leaders_res[$i]["Name"] . "," . $mission_leaders_res[$i]["All"] . "," . ($i+1) . "\n";

	}

	//********************************************* End Mission Leaders Report ***************************************//
	//********************************************* Start EMTs Report ************************************************//

	$emts_str = "Name,Day Urgence,Day AVP,Day Transport,Day Total,Day Ranking,Name,Night Urgence,Night AVP,Night Transport,Night Total,Night Ranking,Name,Total,Total Ranking\n";
	$counter = 0;
	for($i = 0;$i < count($emts_res); $i++){
		$emts_str .= $emts_res_day[$i]["Name"] . "," . $emts_res_day[$i]["Day_Urg"] . "," . $emts_res_day[$i]["Day_AVP"] . "," 
				. $emts_res_day[$i]["Day_Transport"] . "," . $emts_res_day[$i]["Day"] . "," . ($i + 1) . "," . $emts_res_night[$i]["Name"] . ","
				. $emts_res_night[$i]["Night_Urg"] . "," . $emts_res_night[$i]["Night_AVP"] . "," . $emts_res_night[$i]["Night_Transport"] . ","
				. $emts_res_night[$i]["Night"] . "," . ($i + 1) . "," . $emts_res[$i]["Name"] . "," . $emts_res[$i]["All"] . "," . ($i+1) . "\n";

	}

	//********************************************* End EMTs Report **************************************************//


	$time = time();
    \Zipper::make(storage_path('reports/Reports_' . $time . '.zip'))->addString('All.csv',$all_str)->addString('Drivers.csv',$drivers_str)->addString('Mission_leaders.csv',$mission_leaders_str)->addString('EMTs.csv',$emts_str)->close();
   
    return response()->download(storage_path('reports/Reports_' . $time . '.zip'));
	//??TODO
	//?? ADD OTHER REPORTS
    }

    public function verify_access() {
return true;
        //This page is visible to all users
        //return Auth::user()->driver;
    }
}