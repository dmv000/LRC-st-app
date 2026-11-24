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

use App\Http\ORM\UnauthorizedAccess;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\SendMailController;

class BuildManeuverController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function validate_access($type,$title){

	$technique_admin = Auth::user()->memberOf('Maneuver Admin');
	$head_of_station = Auth::user()->memberOf('Head of Station');

	$chef_equipe = Auth::user()->memberOf('Chef Equipe') && $type;

	if($technique_admin || $head_of_station || $chef_equipe){
		return true;
	}else{
		//Log Unauthorized Access 
		return false;
	}

    }	

    public function duplicate() {

	if ( !$this->validate_access ( 0 , 'Duplicate Maneuver' ) ) {

		$r = new \stdClass;
		$r->Status=0;
		$r->Message = "Unauthorized Access";
		return json_encode($r);
	} else {

    		$data = Request::get('form');
		
		try{

			$man = Maneuver::where('id',$data)->first();
			
			if($man == null) {

				//Trying to duplicate unexisting Maneuver;
				$r = new \stdClass;
				$r->Status=0;
				$r->Message = "The mission you are trying to duplicate ";
				return json_encode($r);

			}

			$new_man = $man->replicate();
			while( Maneuver::where('title',$new_man->title)->first() != null ) {

				$new_man->title .= " - Copy"; 
			}
			$new_man->save();

			foreach(ManeuverSection::where('maneuver_id',$man->id)->get() as $sec) {

				$new_sec = $sec->replicate();
				$new_sec->maneuver_id = $new_man->id;
				$new_sec->save();
				foreach(ManeuverQuestion::where('section_id',$sec->id)->get() as $question) {
	
					$new_question = $question->replicate();
					$new_question->section_id = $new_sec->id;
					$new_question->save();
				} 
			}

			$r = new \stdClass;
			$r->Status=1;
			$r->Link = "maneuvers";
			return json_encode($r);


		} catch(\Exception $e) {

			Log::warning($e);
			$r = new \stdClass;
			$r->Status=0;
			$r->Message = "An Error has occured, please contact IT";
			return json_encode($r);
		}
	}
    }
    
    public function results() {

	if ( $this->validate_access ( 1 , 'All Maneuvers' ) ) {

		$man = ManeuverResultHeader::where('is_deleted',0)->orderBy('created_at','desc')->get();

		foreach($man as $m) {

			$m->created_by = User::where('id',$m->created_by)->first()->name;
			$ml = User::where('id',$m->mission_leader)->first();
			$m->mission_leader = $ml == null ? "N/A" : $ml->name;
			$m->title = Maneuver::where('id',$m->maneuver_id)->first()->title;
			$m->link = '?man_id=' . $m->maneuver_id . '&working_id=' . $m->id;
		}

		return view('maneuver_results')->with('man',$man);
	} else {

		return view('errors.404');
	}
    }

    public function view() {

	if ( $this->validate_access ( 1 , 'All Maneuvers' ) ) {

		$man = Maneuver::select('title','id')->where('is_deleted',0)->get();
		return view('maneuvers')->with('man',$man);
	} else {

		return view('errors.404');
	}
    }

    public function index() {

	if ( !$this->validate_access ( 0 , 'Create or Edit Maneuvers' ) ) {

		return view('errors.404');

	} else {


    	//New Maneuver maneuver_id working_id
		try{

			$sec_str = '<div class="row section no-sec-error" style="margin:0px;padding:15px; margin-bottom:15px;border-radius:12px;">'.
				'	<input type="text" style="display: none" value="%%%%" class="sec_id">'.
				'	<div class="col-lg-12 item" style="padding:0px;">'.
				'		<div class="form-group">'.
				'			<span style="float: right;position: inherit;">'.
				'				<img onclick="remove_s(this)"src="images/trash.png" height=25px style="padding-right:50px">'.
				'				<img onclick="hide_s(this)" src="images/down.svg" height=25px>'.
				'			</span>'.
				'			<label class="keep_s" for="sec_title">Section Title</label>'.
				'			<div class="hide_s">'.
				'				<input type="text" onchange="validate_sec(this)" name="sec_title" class="form-control replace_s" placeholder="Title" value="****">'.
				'				<p class="error err1"></p>'.
				'				<label for="title">Section Information</label>'.
				'				<ul class="sortable" id="sortable" style="padding-bottom:10px;">'.
				'				&&&&'.
				'				</ul>'.
				'				<div style="text-align: center;">'.
				'					<button type="button" class="btn btn-danger" onclick="add_question(this)">Add New</button>'.
				'				</div>'.
				'			</div>'.
				'		</div>'.
				'	</div>'.
				'</div>';

			$q_str = '<li class="ui-state-default question no-error" style="border-radius:12px">' .
				'	<input type="text" style="display: none" value="%%%%" class="q_id">'.
				'	<span style="float: right;position: inherit;">'.
				'		<img onclick="remove_q(this)"src="images/trash.png" height=25px style="padding-right:50px">'.
				'		<img onclick="hide_q(this)" src="images/down.svg" height=25px>'.
				'	</span>'.
				'	<label class="keep" for="cont1">Text</label>'.
				'	<div class="hide_q" style="display:block">'.
				'		<textarea onchange="validate(this)" id="cont1" name="sec_title" class="form-control cont" placeholder="Content">****</textarea>'.
				'		<p class="error err2"></p>'.
				'		<label for="comm">Has Comments</label>'.
				'		<div>'.
				'			<input style="position: inherit;margin-left: 0px;margin-right: 5px;" class="option-input checkbox" type="checkbox" value="empty" ++++ />'.
				'		</div>'.
				'		<label for="cont1">Type</label>'.
				'		<div style="font-size:12px;">'.
				'			<label class="radio-inline">'.
				'				<input style="position: inherit;margin-left: 0px;margin-right: 5px;" class="option-input radio" type="radio" value="empty" @@@@ name="type____">Empty'.
				'			</label>'.
				'			<label class="radio-inline">'.
				'				<input style="position: inherit;margin-left: 0px;margin-right: 5px;" class="option-input radio" type="radio" value="boolean" !!!! name="type____">Yes/No'.
				'			</label>'.
				'			<label class="radio-inline">'.
				'				<input style="position: inherit;margin-left: 0px;margin-right: 5px;" class="option-input radio" type="radio" value="text" ^^^^ name="type____">Text'.
				'			</label>'.
				'			<label class="radio-inline">'.
				'				<input style="position: inherit;margin-left: 0px;margin-right: 5px;" class="option-input radio" type="radio" value="title" #### name="type____">Sub Section'.
				'			</label>'.
				'		</div>'.
				'	</div>'.
				'</li>';

			$man_id = Input::get('man_id');

			if($man_id == "") {

				$q_str = str_replace("****", "", $q_str);
				$q_str = str_replace("@@@@", "checked", $q_str);
				$q_str = str_replace("!!!!", "", $q_str);
				$q_str = str_replace("^^^^", "", $q_str);
				$q_str = str_replace("####", "", $q_str);
				$q_str = str_replace("++++", "", $q_str);

				$sec_str = str_replace("****", "", $sec_str);
				$sec_str = str_replace("&&&&", "", $sec_str);

				return view('create_man')->with('q_str',$q_str)
										->with('id',"")
										->with('sec_str',$sec_str)
										->with('title',"")
										->with('content',"");
			}else{
				$man = Maneuver::where('id',$man_id)->first();
				$man->sections = ManeuverSection::where('maneuver_id',$man_id)->where('is_deleted',0)->orderBy('orderr')->get();

				$content = "";
				foreach($man->sections as $section) {
					$str = str_replace("%%%%", $section->id,$sec_str); //Set Id
					$str = str_replace("****", $section->title,$str); // Set Title
					foreach(ManeuverQuestion::where('section_id',$section->id)->where('is_deleted',0)->orderBy('orderr')->get() as $question) {
						$q = $q_str;
						$q = str_replace("%%%%", $question->id, $q);
						$q = str_replace("****", $question->text, $q);
						$q = str_replace("++++", $question->has_comment == "1" ? "checked" : "", $q);
						$q = str_replace("type____", "old_" . $question->id, $q);

						$empty = "";
						$bool = "";
						$text = "";
						$title = "";

						if($question->type == "empty") {
							$empty = "checked";
						}else if($question->type == "boolean") {
							$bool = "checked";
						}else if($question->type == "title") {
							$title = "checked";
						}else {
							$text = "checked";
						}

						$q = str_replace("@@@@", $empty, $q);
						$q = str_replace("!!!!", $bool, $q);
						$q = str_replace("^^^^", $text, $q);
						$q = str_replace("####", $title, $q);


						$str = str_replace("&&&&", $q . "&&&&" , $str);
					}
					$str = str_replace("&&&&", "" , $str); 
					$content .= $str;
				}

				$q_str = str_replace("****", "", $q_str);
				$q_str = str_replace("@@@@", "checked", $q_str);
				$q_str = str_replace("!!!!", "", $q_str);
				$q_str = str_replace("^^^^", "", $q_str);
				$q_str = str_replace("####", "", $q_str);
				$q_str = str_replace("++++", "", $q_str);

				$sec_str = str_replace("****", "", $sec_str);
				$sec_str = str_replace("&&&&", "", $sec_str);

				return view('create_man')->with('q_str',$q_str)
						->with('id', $man->id)
						->with('sec_str',$sec_str)
						->with('title',$man->title)
						->with('content',$content);
			}

} catch (\Exception $e) {
Log::warning($e);
	return view('errors.404');
}

}
    }

    public function store() {

	if ( !$this->validate_access ( 0 , 'Create or Edit Maneuvers POST' ) ) {

		$r = new \stdClass;
		$r->Status=0;
		$r->Message = "Unauthorized Access";
		return json_encode($r);
	} else {


    	$data = Request::get('form');

	File::append(storage_path('logs/Maneuver_Log.txt'),"User : " . Auth::user()->name . "\n");
	File::append(storage_path('logs/Maneuver_Log.txt'),"At : " . date("D M d, Y G:i") . "\n");
	File::append(storage_path('logs/Maneuver_Log.txt'),json_encode($data));
	File::append(storage_path('logs/Maneuver_Log.txt'),"\n");

		try{

			$man_id = "";

			\DB::transaction(function() use($data, &$man_id){

				//$log = new ManeuverLog();
				//$log->

				$man = Maneuver::where('id',$data['id'])->first();

				if($man == null) {
					//New 
					$man = new Maneuver();
					$man->created_by = Auth::user()->id;
				}

				$man->title = $data['title'];
				$man->save();
				$man_id = $man->id;

				//Delete all sections
				ManeuverSection::where('maneuver_id',$man->id)->update(['is_deleted' => 1]);

				$section_counter = 0;
				//Sections
				foreach($data['sections'] as $section) {
					
					//Check if exists
					$sec_id = $section['id'];
					if($sec_id == "%%%%") {
						//New Section
						$sec = New ManeuverSection();
						$sec->maneuver_id = $man->id;
					}else {
						$sec = ManeuverSection::where('id',$sec_id)->first();
						$sec->is_deleted = 0;
					}
					//Set Title
					$sec->title = $section['title'];
					$sec->orderr = $section_counter;
					$section_counter += 1;
					$sec->save();

					//Delete all questions
					ManeuverQuestion::where('section_id',$sec->id)->update(['is_deleted' => 1]);

					$question_counter = 0;
					//Questions
					foreach($section['questions'] as $question) {

						//Check if new
						$q_id = $question['id'];
						if($q_id == "%%%%") {
							//New Question
							$q = new ManeuverQuestion();
							$q->section_id = $sec->id;
						}else {
							$q = ManeuverQuestion::where('id',$q_id)->first();
							$q->is_deleted = 0;
						}
						$q->text = $question['text'];
						$q->type = $question['type'];
						$q->orderr = $question_counter;
						$question_counter += 1;
						$q->has_comment = $question['has_comment'];
						$q->save();
					}
				}
			});

			$r = new \stdClass;
			$r->Status=1;
			$r->Link = "build_maneuver?man_id=" . $man_id;
			$r->Link = "maneuvers";
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
}
