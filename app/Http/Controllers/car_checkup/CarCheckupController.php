<?php

namespace App\Http\Controllers\car_checkup;

use Request;
use DateTime;
use Auth;
use File;

use App\Responses\Response;
use App\User as EMT;

use App\Http\ORM\CarCheckup;

use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use App\Http\ORM\CarCheckup as Checkup;
use App\Http\ORM\CarCheckupQuestion as Question;
use App\Http\ORM\CarCheckupQuestionType as QuestionType;

use App\Http\ORM\CarCheckupHeader as CheckupHeader;
use App\Http\ORM\CarCheckupDetails as CheckupDetail;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class CarCheckupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCarCheckup() {

	if(!$this->verify_access(2)) {
            //Log 
            unauthorized('GetCarCheckup View',Auth::user());
            return view('errors.404');
        }

	$id = Input::get('id');
	
	if( !isset($id) || is_null($id) || empty($id) ) {
		$tableData = array();
		$car_number = '';
		$id = '';
	} else {
		$checkup = Checkup::where('id' , $id)->first();
	 	if( is_null($checkup) ) {
            		return view('errors.404');
		}
		$car_number = $checkup->car_number;
		$tableData = [];
		$questions = Question::where('car_checkup_id' , $id)->where('is_deleted' , 0 )->where('version',$checkup->current_version)->orderBy('order', 'ASC')->get();
		foreach( $questions as $question ) {

			$arr = [];
			$arr['id'] = $question->id;
			$arr['title_en'] = str_replace("&#039;","'",$question->title_english);
			$arr['title_ar'] = $question->title_arabic;
			$arr['type'] = QuestionType::where('id' , $question->car_checkup_question_type_id)->first()->description;
			$arr['min'] = $question->min;
			$arr['max'] = $question->max;
			$arr['step'] = $question->step;
			$arr['default'] = $question->default_value;
			$arr['source'] = $question->image_source;

			$tableData[] = $arr;
		}
	}
	return view('car_checkup.add_edit_checkup', compact('car_number' , 'tableData' , 'id'));

    }
	
	public function duplicate() {
		
		$id = Request::get('form');;
		if(!$this->verify_access(2)) {
            //Log 
            unauthorized('DuplicateCarCheckup View',Auth::user());
            $response = new Response();
			$response->Status = 0;
			$response->Message = "Unaithorized Access";
			return json_encode($response);
        }
		
		try {
			\DB::transaction(function() use ($id) {
				
				$old = Checkup::where('id',$id)->where('is_deleted',0)->first();
				if( $old == null ) {
					$response = new Response();
					$response->Status = 0;
					$response->Message = "Checkup doesn't exist";
					Log::warning(json_encode($response));
					throw new \Exception($response->Message);
				}
				
				$new = $old->replicate();
				$iterations = 0; 
				while( Checkup::where('car_number',$new->car_number)->first() != null && $iterations < 100 ) {
					$new->car_number = $new->car_number+ 100;
					$iterations += 1;
				}
				$new->save();
				$newId = $new->id;
				
				$oldQs = Question::where('car_checkup_id',$id)->get();
				
				foreach( $oldQs as $oldQ ) {
					
					$newQ = $oldQ->replicate();
					$newQ->car_checkup_id = $newId;
					$newQ->save();
				}
				
			});
			
			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
		} catch (\Exception $e) {
			
			Log::error($e);
			$response = new Response;
			$response->Status = 0;
			$response->Message = $e->getMessage();
			return json_encode($response);
		}
	}
	
	public function delete() {
		
		$id = Request::get('form');;
		if(!$this->verify_access(2)) {
            //Log 
            unauthorized('DeleteCarCheckup View',Auth::user());
            $response = new Response();
			$response->Status = 0;
			$response->Message = "Unaithorized Access";
			return json_encode($response);
        }
		
		try {
			\DB::transaction(function() use ($id) {
				
				$old = Checkup::where('id',$id)->where('is_deleted',0)->first();
				if( $old == null ) {
					$response = new Response();
					$response->Status = 0;
					$response->Message = "Checkup doesn't exist";
					Log::warning(json_encode($response));
					throw new \Exception($response->Message);
				}
				
				$old->is_deleted = 1;
				$old->save();
				
			});
			
			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
		} catch (\Exception $e) {
			
			Log::error($e);
			$response = new Response;
			$response->Status = 0;
			$response->Message = $e->getMessage();
			return json_encode($response);
		}
	}
	
	public function deleteCarCheckupDetails() {
		
		$id = Request::get('form');;
		if(!$this->verify_access(2)) {
            //Log 
            unauthorized('DeleteCarCheckupDetails View',Auth::user());
            $response = new Response();
			$response->Status = 0;
			$response->Message = "Unaithorized Access";
			return json_encode($response);
        }
		
		try {
			\DB::transaction(function() use ($id) {
				
				$old = CheckupHeader::where('id',$id)->where('is_deleted',0)->first();
				if( $old == null ) {
					$response = new Response();
					$response->Status = 0;
					$response->Message = "Checkup doesn't exist";
					Log::warning(json_encode($response));
					throw new \Exception($response->Message);
				}
				
				$old->is_deleted = 1;
				$old->save();
				
			});
			
			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
		} catch (\Exception $e) {
			
			Log::error($e);
			$response = new Response;
			$response->Status = 0;
			$response->Message = $e->getMessage();
			return json_encode($response);
		}
	}

    public function buildCarCheckup() {

	$s = Request::get('form');
	File::append(storage_path('logs/Build_Car_Checkup_Log.txt'),json_encode($s)."\n");

	try {
	
	    \DB::transaction(function() use ($s) {
		$response = new Response;
		
		$c_id = $s['id'];
		if( empty($c_id) ) {

			$checkup = new Checkup();
			$checkup->save();
			$c_id = $checkup->id;
			$version = 1;
		} else {
			$checkup = Checkup::where('id' , $c_id)->first();
			$version = $checkup->current_version + 1;
			if( is_null($checkup) ) {

			    $response->Status = 0;
			    $response->Message = "Checkup doesn't exist";
			    Log::warning(json_encode($response));
			    throw new \Exception($reponse->Message);
			}
		}
		
		$counter = 1;
		if ( isset($s['questions']) ) {
			foreach( $s['questions'] as $q ) {

				//$q_id = $q['id'];
				//if( empty($q_id) ) {

					$question = new Question();
					$question->car_checkup_question_type_id = QuestionType::where('description' , $q['type'])->first()->id;
				//} else {
				//
				//	$question = Question::where('id' , $q_id )->first();
				//	if( is_null($question) || $question->car_checkup_question_type_id != QuestionType::where('description' , $q['type'])->first()->id ) {
				//
				//	    $response->Status = 0;
				//	    $response->Message = "Checkup Question doesn't exist";
				//	    Log::warning(json_encode($response));
				//	    throw new \Exception($reponse->Message);
				//	}
				//}
	
				$question->title_english 	= is_null($q['title_en']	) ? '' : $q['title_en'];
				$question->title_arabic 	= is_null($q['title_ar']	) ? '' : $q['title_ar'];
				$question->default_value 	= is_null($q['default']		) ? '' : $q['default'];
				$question->image_source 	= is_null($q['source']		) ? '' : $q['source'];
				$question->order 		= $counter;
				$question->car_checkup_id 	= $c_id;
				$question->version 		= $version;
				$question->min 			= is_null($q['min']		) ? '' : $q['min'];
				$question->max 			= is_null($q['max']		) ? '' : $q['max'];
				$question->step 		= is_null($q['step']		) ? '' : $q['step'];
				$question->save();
				$counter = $counter + 1;
			}
		}
		$checkup->car_number = $s['title'];
		$checkup->current_version = $version;
		$checkup->save();

		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->event_type = EventType::where('description','Add/Edit Car Checkup')->first()->id;
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

    
    public function verify_access( $type ) {

        //This page is visible to all drivers
	//Access type 1 -> All Drivers
	//Access type 2 -> Only Admin
	if( $type == 1 ) {
		return Auth::user()->memberOf('Driver');
	} else {
		return Auth::user()->memberOf('Drivers Admin');
	}
    }

	public function getAllCarCheckups(){
	
        if(!$this->verify_access(1)) {
            //Log 
            unauthorized('Get CarCheckupController View',Auth::user());
            return view('errors.404');
        }
		$page_title = "Car Checkup";
		$tableData = CarCheckup::select('id','car_number','current_version')->where('is_deleted',0)->get();
//return json_encode($tableData);

		return view('car_checkup.car_checkup_view', compact('tableData','page_title'));	
	}
	
	public function carCheckupResults(){
		if(!$this->verify_access(2)) {
            unauthorized('carCheckupResults View',Auth::user());
            return view('errors.404');
        }
		$drivers = EMT::where('driver',1)->select('id','nickname')->get();
		$checkups = CarCheckup::select('id','car_number')->where('is_deleted',0)->get();
		return view('car_checkup.car_checkup_results', compact('drivers','checkups'));
	}
	
	public function searchCarCheckupResults(){
		if(!$this->verify_access(2)) {
            //Log 
            unauthorized('searchCarCheckupResults',Auth::user());
            $response = new Response();
			$response->Status = 0;
			$response->Message = "Unaithorized Access";
			return json_encode($response);
        }
		
		$request = Request::get('form');
		$response = New Response;
		try {
			
			$from_time = $request["from_date"];
			$to_time = $request["to_date"];
			
			$driver = $request["driver"];
			$checkup = $request["checkup"];
			
			$records = [];
			$data = CheckupHeader::whereBetween('created_at' , [ $from_time , $to_time . ' 23:59:59' ])->where('is_deleted',0);
			if($driver!="-1"){
				$data = $data->where('created_by',$driver);
			}
			if($checkup!="-1"){
				$data = $data->where('car_checkup_id',$checkup);
			}
			$data = $data->get();
			foreach($data as $record){
				$rec = [];
				$rec["id"] = $record->id;
				$c = Checkup::where('id',$record->car_checkup_id)->first();
				if($c==null){
					$rec["car_number"] = "N/A";
				} else {
					$rec["car_number"] = $c->car_number;
				}
				$rec["driver"] = $record->driver_name;
				$driver = EMT::where('id',$record->created_by)->first();
				if( $driver==null ){
					$rec["filledBy"] = "N/A";
				} else {
					$rec["filledBy"] = $driver->nickname;
				}
				$rec["date"] = $record->checkup_date;
				$rec["submitDate"] = $record->created_at->format('Y-m-d H:i');
				$records[] = $rec;
			}
			$response->Status = 1;
			$response->Data = json_encode($records);
            return json_encode($response);
			
		} catch ( \Exception $e ) {
			
			Log::warning("Search Car Checkup Results Exception");
			Log::warning($e); 
            $response->Status = 0;
            return json_encode($response);
		}
	}
	
	public function carCheckupDetails(){
		if(!$this->verify_access(2)) {
            //Log 
            unauthorized('carCheckupDetails',Auth::user());
            $response = new Response();
			$response->Status = 0;
			$response->Message = "Unaithorized Access";
			return json_encode($response);
        }
		
		$id = Request::get('form');
		$response = New Response;
		try {
			
			$checkup = CheckupHeader::where('id', $id)->first();
			$carCheckup = Checkup::where('id',$checkup->car_checkup_id)->first();
			
			$det = "[";
			$det .= '{"name":"Date", "type":"date","value":"'.$checkup->checkup_date.'"}';
			$det .= ',{"name":"Driver", "type":"driver","value":"'.$checkup->driver_name.'"}';
			$driver = EMT::where('id',$checkup->created_by)->first();
			if( $driver==null ){
				$det .= ',{"name":"Filled By", "type":"driver","value":"N/A"}';
			} else {
				$det .= ',{"name":"Filled By", "type":"driver","value":"'.$driver->nickname.'"}';
			}
			$det .= ',{"name":"Submitted", "type":"date","value":"'.$checkup->created_at->format("Y-m-d H:i").'"}';
			$det .= ',{"name":"Car","value":"'.$carCheckup->car_number.'"}';
			
			$info = Question::where('car_checkup_id',$checkup->car_checkup_id)
								->where('version',$checkup->version)
								->orderBy('order', 'ASC')->get();
								
			foreach($info as $q){
				$value = CheckupDetail::where('car_checkup_question_id', $q->id)->
									where('car_checkup_header_id', $id)->get();
				$val = "";
				if($q->car_checkup_question_type_id == "1" || $q->car_checkup_question_type_id == "2" || 
						$q->car_checkup_question_type_id == "5" ){
					if($value==null || sizeof($value)==0){
						$val = "";
					} else {
						$val = $value[0]->value;
					}
				} else if ($q->car_checkup_question_type_id == "3"){
					if($value==null || sizeof($value)==0) {
						$val = "false"; 
					} else {
						$val = "true";
					}
				} else if ($q->car_checkup_question_type_id == "6"){
					if($value==null || sizeof($value)==0){
						$val = "false,false";
					} else if( sizeof($value)==1 ){
						if($value[0]->value=="left"){
							$val = "true,false";
						} else {
							$val = "false,true";
						}
					} else {
						$val = "true,true";
					}
				} else if ($q->car_checkup_question_type_id == "4"){
					$marks = "[";
					$sep = "";
					foreach($value as $mark){
						$marks .= $sep . str_replace("\"","",$mark->value);
						$sep = ",";
					}
					$val = $marks . "]";
				}
				
				$det .= ',{"src":"'.$q->image_source.'","name":"'.$q->title_english.'/'.$q->title_arabic.'", "value":"'.$val.'", "min":"'.$q->min.'"'.
								', "max":"'.$q->max.'", "default":"'.$q->default_value.'", "type" : "'.$q->car_checkup_question_type_id.'"}';
				
			}
			
			$response->Status = 1;
			$response->Data = null;
			$response->Message = $det . "]";
            return json_encode($response);
			
		} catch ( \Exception $e ) {
			
			Log::warning("Search Car Checkup Results Exception");
			Log::warning($e); 
            $response->Status = 0;
            return json_encode($response);
		}
	}

}
