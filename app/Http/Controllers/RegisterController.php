<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;
use File;
use Illuminate\Support\Facades\Log;

use App\Http\ORM\Register;
use App\Http\ORM\RegisterChangeLog as ChangeLog;


use App\User;

use App\Http\ORM\ShiftEMT;
use App\Http\ORM\ShiftWork;
use App\Http\ORM\ShiftTopic;
use App\Http\ORM\ShiftStatus;
use App\Http\ORM\ShiftRequest;
use App\Http\ORM\ShiftParticipant;
use App\Http\ORM\StationWork;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;
use App\Responses\Response;
use App\Jobs\SendRecyclageEmail;

use Illuminate\Support\Facades\Input;

class RegisterController extends Controller
{

	
	
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function summary() {

		try {


			$curr_date = date('Y-m-d');
			$month_val = strtotime('1 month');
			$next_month_y = date('Y');//, $month_val);
			$next_month_m = date('m');//, $month_val);

			$year = Input::get('year') == "" ? $next_month_y : Input::get('year');
			$month = Input::get('month') == "" ? $next_month_m : Input::get('month');

			$registers = Register::whereMonth('register_date',$month)->whereYear('register_date',$year)
				->select('id','finished',
					\DB::raw('year(register_date) as year'),
					\DB::raw('month(register_date) as month'),
					\DB::raw('day(register_date) as day'))->get();

		$page_title = "All Register";
		$user_events = [];
		return view('all_registers', compact( 'year' , 'month' , 'registers' , 'page_title' ));




		}catch(\Exception $e){
			Log::warning($e);		
            		return view('errors.404');
        	} catch (\Throwable $e) {
			Log::warning($e);		
            		return view('errors.404');

		} catch (\Error $e) {
			Log::warning($e);		
            		return view('errors.404');
		}
	}

	public function index() {


if( !Auth::user()->memberOf('Chef Equipe') and !Auth::user()->memberOf('Secretariat Members') ) return view('errors.404');

		try {

			$register_id = Input::get('register_id');

			if( $register_id == null || $register_id == "" ) {

				return view('add_register');
			} else {

				$ret = Register::select(\DB::raw('DATE_FORMAT(register_date,\'%m/%d/%Y\') as date'),'id','skills','special_missions')->where('id' , $register_id)->first();
				if($ret == null) {
					Log::error('Failed Searching for Register with ID : ' . $register_id);
					return view('errors.404');
				}

				$emts = ShiftEMT::where('shift_id' , $register_id )->where('is_deleted' , 0 )
						->select('arrival' , 
								'checkup' , 
								'departure' , 
								'id' , 
								'shift_status_id' ,
								'double_s',
								'badal',
								'user_id','work_description')->get();

				foreach( $emts as $emt ) {

					/*$str = "";
					$str2 = "";

					foreach( ShiftWork::where('shift_emt_id' , $emt->id )->where('is_deleted' , 0 )->pluck('work_id')->toArray() as $work_id ) {

						$work = StationWork::where('id',$work_id)->first();

						if( $work != null ) {
							if($str != "") {
								$str.= ",";
								$str2 .= ",";
							}
							$str .= $work->id;
							$str2 .= $work->description;
						}
					}

					$emt->work_ids = $str;
					$emt->work = $str2;*/

					$emt->name = User::where('id' , $emt->user_id )->first()->nickname;
					if($emt->badal == -1 ) {
						$emt->badal_name = "";
					} else {
						$emt->badal_name = User::where('id' , $emt->badal )->first()->nickname;
					}
					$emt->shift_status = ShiftStatus::where('id' , $emt->shift_status_id)->first()->description;
				}

				$topics = ShiftTopic::where('shift_id' , $register_id )->where('is_deleted' , 0 )
						->select(
								'id','time','topic','comments'
							)->get();

				$requests = ShiftRequest::where('shift_id' , $register_id )->where('is_deleted' , 0 )
						->select('id','type','request')->get();
							
				
				$participants = ShiftParticipant::where('is_deleted' , 0 )->where('shift_id' , $ret->id)->pluck('user_id')->toArray();

				return view('add_register')->with('date',$ret->date)->with('register_id',$ret->id)->with('participants' , $participants)->with('emts',$emts)
									->with('skills',$ret->skills)->with('special_missions',$ret->special_missions)
									->with('topics',$topics)->with('requests',$requests);

			}

		
		}catch(\Exception $e){
			Log::warning($e);		
            		return view('errors.404');
        	} catch (\Throwable $e) {
			Log::warning($e);		
            		return view('errors.404');

		} catch (\Error $e) {
			Log::warning($e);		
            		return view('errors.404');
		}

	}
	
	private function cl( $type , $old , $new , $id) {
		$cl = New ChangeLog();
		$cl->type = $type;
		$cl->new = $new;
		$cl->old = $old;
		$cl->user_id = Auth::user()->id;
		$cl->shift_id = $id;
		return $cl;
	}

    public function store(){

	$log_change = true;

        $s = json_decode(json_encode(Request::get('form')) , true);
        File::append(storage_path('logs/Add_Edit_Register.txt'),json_encode($s) . "\n");
		
		$changes = array();
		
		$response = New Response;
        $response->Status = 0;

        try{
			$s = (object)$s;
			$register = new Register();
			
			$alreadySent = false;
			if( $s->id != 0 ) {
				$register = Register::where('id' , $s->id)->first();
				if( $register == null ) {

					$response->Message = "You are updating a non existing Register. Please Contact IT";
					Log::warning( json_encode( $response ) );
					return json_encode( $response );
				}
				
				if( $register->finished == 1 && !Auth::user()->memberOf('Secretariat Admin') ) {

					$response->Message = "Register marked as Finished. Cannot Update Anymore";
					Log::warning( json_encode( $response ) );
					return json_encode( $response );
				}
				
				if( $register->finished == 1 ){
					$alreadySent = true;
				}
			}

			$register->created_by = Auth::user()->id;
			
			$date = DateTime::createFromFormat("m\/d\/Y",trim($s->date))->format("Y-m-d");
			if( $register->id != 0 && $register->register_date != $date ) {
				$changes[] = $this->cl("Date" , $register->date , $date,0);
			}
			$register->register_date = $date;
			
			if( $register->id != 0 && $register->skills != trim($s->skills) ) {
				$changes[] = $this->cl("Skills" , $register->skills , trim($s->skills),0);
			}
			$register->skills = trim($s->skills);
			
			if( $register->id != 0 && $register->special_missions != trim($s->special_missions) ) {
				$changes[] = $this->cl("Special Missions" , $register->special_missions , trim($s->special_missions),0);
			}
			$register->special_missions = trim($s->special_missions);
			
			$register->finished = $s->finished;
			
			\DB::transaction(function() use ($s,$register,$changes,$log_change,$alreadySent) {
			
				$register->save();
				
				ShiftEMT::where('shift_id', $register->id)->update(['is_deleted'=>1]);
				//ShiftWork::where('shift_id', $register->id)->update(['is_deleted'=>1]);
				ShiftTopic::where('shift_id', $register->id)->update(['is_deleted'=>1]);
				ShiftRequest::where('shift_id', $register->id)->update(['is_deleted'=>1]);
				ShiftParticipant::where('shift_id', $register->id)->update(['is_deleted'=>1]);

				if( isset( $s->additional_participant ) && $s->additional_participant != null && sizeof($s->additional_participant ) > 0 ) {

					foreach( $s->additional_participant as $participant ) {

						if( $participant != "" ) {
						
							$sp = ShiftParticipant::where('shift_id' , $register->id )->where('user_id' , $participant)->first();
							if($sp == null) {

								if( $s->id != 0) {
									$changes[] = $this->cl('Add New Participant' , '' , $participant , $register->id);
								}
								$sp = new ShiftParticipant();
								$sp->shift_id = $register->id;
								$sp->user_id = $participant;
								$sp->save();
							} else {

								$sp->is_deleted = 0;
								$sp->save();
							}
						}
					}
					if( $s->id != 0) {

						foreach( ShiftParticipant::where('is_deleted' , 1)->where('shift_id' , $register->id)->get() as $part ) {

							$changes[] = $this->cl('Remove Participant' , $part->user_id , '' , $register->id);	
						}
					}
				}

				//Requestes
				if( isset( $s->requests ) && $s->requests != null && sizeof($s->requests) > 0 ) {
									
					foreach ( $s->requests as $request ) {
					
						$request = (object)$request;
						if( $request->id != "" ) {
						
							$sr = ShiftRequest::where('id' , $request->id)->first();
							if( $sr == null ) {
								Log::warning('Editing Request not found. Request ID : ' . $request->id . '. Shift Id : ' . $register->id);
								throw new Exception('Editing Request not found');
							}
							$sr->is_deleted = 0;
							
							if( $sr->request != trim($request->request) ) {
								$changes[] = $this->cl("Shift Request Request" , $sr->request , trim($request->request) , $sr->id);
							}
							$sr->request = trim($request->request);

							if( $sr->type != trim($request->type) ) {
								$changes[] = $this->cl("Shift Request Type" , $sr->type , trim($request->type) , $sr->id);
							}
							$sr->type = trim($request->type);
							$sr->save();
							
						} else {
						
							$sr = New ShiftRequest();
							$sr->shift_id = $register->id;
							$sr->request = trim( $request->request );
							$sr->type = trim( $request->type );
							$sr->save();
						
						}						
					}					
				}
			
			
				//Topics
				if( isset( $s->topics ) && $s->topics != null && sizeof($s->topics) > 0 ) {
									
					foreach ( $s->topics as $topic ) {
					
						$topic = (object)$topic;
						if( $topic->id != "" ) {
						
							//Editing
							$st = ShiftTopic::where('id' , $topic->id)->first();
							if( $st == null ) {
								Log::warning('Editing Topic not found. Topic ID : ' . $topic->id . '. Shift Id : ' . $register->id);
								throw new Exception('Editing Topic not found');
							}
							$st->is_deleted = 0;
							
							if( $st->topic != trim($topic->topic) ) {
								$changes[] = $this->cl("Shift Topic Topic" , $st->topic , trim($topic->topic) , $st->id);
							}
							$st->topic = trim($topic->topic);

							if( $st->time != trim($topic->time) ) {
								$changes[] = $this->cl("Shift Topic Time" , $st->time , trim($topic->time) , $st->id);
							}
							$st->time = trim($topic->time);

							if( $st->comments != trim($topic->comments) ) {
								$changes[] = $this->cl("Shift Topic Comments" , $st->comments , trim($topic->comments) , $st->id);
							}
							$st->comments = trim($topic->comments);
							$st->save();
							
						} else {
						
							//New
							$st = New ShiftTopic();
							$st->topic = $topic->topic;
							$st->time = trim($topic->time);
							$st->comments = trim($topic->comments);
							$st->shift_id = $register->id;
							$st->save();
						}
					
					}
					
				}
			
				//EMTs
				if( isset( $s->emts ) && $s->emts != null && sizeof($s->emts) > 0 ) {
									
					foreach ( $s->emts as $emt ) {
						$emt = (object)$emt;
						if ( $emt->id != null && $emt->id != "" ) {
							//Editing
							$se = ShiftEMT::where('id' , $emt->id)->first();
							if( $se == null ) {
								Log::warning('Editing EMT not found. EMT ID : ' . $emt->id . '. Shift Id : ' . $register->id);
								throw new Exception('Editing EMT not found');
							}
							$se->is_deleted = 0;
							
							if( $se->user_id != $emt->user_id ) {
								$changes[] = $this->cl("Shift EMT User" , $se->user_id , $emt->user_id , $se->id);
							}
							$se->user_id = $emt->user_id;
							
							if( $se->badal != $emt->badal ) {
								$changes[] = $this->cl("Badal" , $se->badal , $emt->badal , $se->id);
							}
							$se->badal = $emt->badal;

							if( $se->arrival != trim($emt->arrival) ) {
								$changes[] = $this->cl("Shift EMT Arrival" , $se->arrival , trim($emt->arrival) , $se->id);
							}
							$se->arrival = trim($emt->arrival);
							
							if( $se->departure != trim($emt->departure) ) {
								$changes[] = $this->cl("Shift EMT Departure" , $se->departure , trim($emt->departure) , $se->id);
							}
							$se->departure = trim($emt->departure);		

							if( $se->double_s != trim($emt->double_s) ) {
								$changes[] = $this->cl("Shift EMT Double" , $se->double_s , trim($emt->double_s) , $se->id);
							}
							$se->double_s = trim($emt->double_s);	

							if( $se->checkup != trim($emt->checkup) ) {
								$changes[] = $this->cl("Shift EMT Checkup" , $se->checkup , trim($emt->checkup) , $se->id);
							}
							$se->checkup = trim($emt->checkup);		

							if( $se->shift_status_id != trim($emt->shift_status_id) ) {
								$changes[] = $this->cl("Shift EMT Shift Status" , $se->shift_status_id , trim($emt->shift_status_id) , $se->id);
							}
							$se->shift_status_id = trim($emt->shift_status_id);		

							if( $se->work_description != trim($emt->work_description) ) {
								$changes[] = $this->cl("Shift Work" , $se->work_description , trim($emt->work_description) , $se->id);
							}
							$se->work_description = trim($emt->work_description);		
							$se->save();
							
							/*if( $emt->work_ids != null and $emt->work_ids != "" ) {
							
								foreach( explode( "," , $emt->work_ids ) as $work ) {
									if( $work != null && $work != "" ) {
										$sew = new ShiftWork(); 
										$sew->user_id = $emt->user_id;
										$sew->shift_emt_id = $se->id;
										$sew->shift_id = $register->id;
										$sew->work_id = $work;
										$sew->save();
									}
								}
							}*/
							
						} else {
							//New
							$se = New ShiftEMT();
							$se->shift_id = $register->id;
							$se->user_id = $emt->user_id;
							$se->arrival = trim($emt->arrival);
							$se->checkup = trim($emt->checkup);
							$se->departure = trim($emt->departure);
							$se->shift_status_id = trim($emt->shift_status_id);
							$se->double_s = trim($emt->double_s);
							$se->badal = trim($emt->badal);
							$se->work_description = trim($emt->work_description);
							$se->save();
							
							/*if( $emt->work_ids != null and $emt->work_ids != "" ) {
							
								foreach( explode( "," , $emt->work_ids ) as $work ) {
									if( $work != null && $work != "" ) {
										$sew = new ShiftWork(); 
										$sew->user_id = $emt->user_id;
										$sew->shift_emt_id = $se->id;
										$sew->shift_id = $register->id;
										$sew->work_id = $work;
										$sew->save();
									}
								}
							}*/
							
						}				
					}
				}
				
				if( $log_change == true ) {
					foreach( $changes as $change ) {
				
						if( $change->shift_id == 0 ) {
							$change->shift_id = $register->id;
						} else {
							$change->type .= $change->shift_id;
							$change->shift_id = $register->id;
						}
						$change->save();
					}
				}
				if( $s->finished == 1 && trim( $register->skills ) !== '' && !$alreadySent ) {
				
					SendRecyclageEmail::dispatch($register)->onQueue('app_1');
				}
			});

			$event = new Event;
			$event->user_id = Auth::user()->id;
			$event->event_type = EventType::where('description','Add/Edit Register')->first()->id;
			$event->save();

        }catch(\Exception $e){
			Log::warning($e);
            $response->Message="An Error Occured. Please Contact IT";			
            return json_encode($response);
        }
        $response->Status = 1;
        return json_encode($response);
        
    }

}
