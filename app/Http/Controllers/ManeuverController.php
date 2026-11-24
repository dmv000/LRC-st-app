<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Request;
use Excel;
use Auth;
use File;


use App\User;

use App\Http\ORM\Maneuver;
use App\Http\ORM\ManeuverSection;
use App\Http\ORM\ManeuverQuestion;

use App\Http\ORM\ManeuverResultHeader;
use App\Http\ORM\ManeuverResultDetails;
use App\Http\ORM\ManeuverResultData;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class ManeuverController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function verify_access() {

        return Auth::user()->memberOf('Chef Equipe');
    }

    public function index() {

    	//New Maneuver maneuver_id working_id
try{
    	$man_id = Input::get('man_id');
    	$working_id = Input::get('working_id');

    	if($working_id == "") {
    		$man_res = new ManeuverResultHeader();
    		$man_res->maneuver_id = $man_id;
    		$man_res->created_by = Auth::user()->id; 
    		$man_res->date = date('Y-m-d');
		$man_res->is_deleted = 1;
    		$man_res->save();
    		$working_id = $man_res->id;
    	}else {
    		$man_res = ManeuverResultHeader::where('id',$working_id)->first();	
    	}

    	$man_res_dt_ids = ManeuverResultDetails::where('maneuver_result_id',$working_id)->pluck('id');
    	$man_sec_com = ManeuverResultDetails::where('maneuver_result_id',$working_id)->select('maneuver_section_id','comment')->get();

    	$man_res_dat = ManeuverResultData::whereIn('man_res_id', $man_res_dt_ids)->select('value','man_question_id','comment')->get();

    	$man_id = $man_res->maneuver_id;

        $man = Maneuver::where('id',$man_id)->first();
        $man->working_id = $working_id;

        $man->ml = $man_res->mission_leader;
        $man->start_time = $man_res->start_time;
        $man->emts = explode(",",$man_res->emts);
        $man->sections = ManeuverSection::where('maneuver_id',$man->id)->where('is_deleted',0)->get();
        $man->is_finished = $man_res->is_finished;
        foreach($man->sections as $section) {
        	$section->questions = ManeuverQuestion::where('section_id',$section->id)->where('is_deleted',0)->get();

        	foreach($section->questions as $question) {
        		if($question->type != "empty") {
	        		$question_res = $man_res_dat->where('man_question_id',$question->id)->first();
        			$question->value = $question_res == null ? "" : $question_res->value;
				$question->comment = $question_res == null ? "" : $question_res->comment;
        		}
        	}
        	$sec_com = $man_sec_com->where('maneuver_section_id',$section->id)->first();
        	$section->comment =  $sec_com == null ? "" : $sec_com->comment;
        }

        return view('maneuver')->with('maneuver',$man)->with('users',User::where('is_deleted',0)->orderBy('name')->get());
} catch (\Exception $e) {Log::warning($e);
	return json_encode($e);
}
    }

    public function store() {

    	$data = Request::get('form');

	File::append(storage_path('logs/Do_Maneuver_Log.txt'),"User : " . Auth::user()->name . "\n");
	File::append(storage_path('logs/Do_Maneuver_Log.txt'),"At : " . date("D M d, Y G:i") . "\n");
	File::append(storage_path('logs/Do_Maneuver_Log.txt'),json_encode($data));
	File::append(storage_path('logs/Do_Maneuver_Log.txt'),"\n");

    //Log::warning($data);
		try{

			$working_id = "";
			$man_id = "";
			\DB::transaction(function() use($data,&$working_id,&$man_id){

				$man_res = ManeuverResultHeader::where('id',$data['id'])->first();
				$man_res->is_deleted = 0;
				$man_res->start_time = $data['start_time'];
				$man_res->mission_leader = $data['mission_leader'];
				$man_res->emts = $data['emts'];
				$man_res->is_finished = $data['finished'];
				if($data['finished'] == 1) {
					$man_res->end_time = date("H:i:s", time());
				}
				$man_res->save();
				$working_id = $man_res->id;
				$man_id = $man_res->maneuver_id;

				//SectionsLog
				foreach($data['sections'] as $section) {
					//Check if exists
					$sec_id = $section['section_id'];
					$man_sec = ManeuverResultDetails::where('maneuver_result_id',$data['id'])
														->where('maneuver_section_id',$sec_id)
														->first();

					if($man_sec == null) {

						$man_sec = new ManeuverResultDetails();
						$man_sec->maneuver_result_id = $data['id'];
						$man_sec->maneuver_section_id = $sec_id;
					}

					if( !empty( $section['comments']  ) ) {
					//if($section['comments'] != null){
						$man_sec->comment = $section['comments'];
					}

					$man_sec->save();
					unset($section['comments']);
					unset($section['section_id']);
					//Questions

					foreach($section as $key => $value) {

						if( ctype_digit ( $key ) ) {

							$man_data = ManeuverResultData::where('man_res_id',$man_sec->id)
											->where('man_question_id',$key)
											->first();

							if($man_data == null) {
								$man_data = new ManeuverResultData();
								$man_data->man_res_id = $man_sec->id;
								$man_data->man_question_id = $key;
							}

							$man_data->value = $value;
							$man_data->save();

						} else {

							$key = substr ( $key , 0 , -4);
							$man_data = ManeuverResultData::where('man_res_id',$man_sec->id)
											->where('man_question_id',$key)
											->first();

							if($man_data == null) {
								$man_data = new ManeuverResultData();
								$man_data->man_res_id = $man_sec->id;
								$man_data->man_question_id = $key;
							}

							$man_data->comment = $value;
							$man_data->save();

						}
					}
				}
			});

			$r = new \stdClass;
			$r->Status=1;
			$r->Link = "maneuver?man_id=" . $man_id . "&working_id=" . $working_id;
			return json_encode($r);

		}catch(\Exception $e){
			Log::warning($e);
			$r = new \stdClass;
			$r->Status=0;
			$r->Message = "Failed. Contact IT";
			return json_encode($r);	
		}


    }
}
