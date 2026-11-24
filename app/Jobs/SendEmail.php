<?php

namespace App\Jobs;

use App\Http\ORM\Urgence;
use App\Http\ORM\Survey;

use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;
use Mail;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $id;

    public function __construct(Urgence $urgence)
    {
        $this->id = $urgence->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    private function sendEmail($email,$username,$guid,$leader_email) {

    	Log::debug('Send Email Request : ' . $email . ' ' . $username . ' ' . $guid);
    	try {

		$link_internal = env('SURVEY_INTERNAL_URL');
		$link_external = env('SURVEY_EXTERNAL_URL');

		if( $leader_email == true ) {

			$body = '<h3>Dear ' . $username . "&nbsp;</h3><br/><br/><p>Please visit <a href='" . $link_internal . "/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to 'LRC WIFI',</big> to fill Driver Assesment Form.</p><br/><p>Or visit <a href='" . $link_external . "/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to any Network,</big> to fill Driver Assesment Form.</p><br/><br/><p>Please visit <a href='" . $link_internal . "/up?id=" . $this->id . "'>this link</a>, <big>while connected to 'LRC WIFI',</big> to upload Mission Documents.</p><br/>Regards<br/>LRC 502 IT Department";
		}else {

			$body = '<h3>Dear ' . $username . "&nbsp;</h3><br/><br/><p>Please visit <a href='" . $link_internal . "/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to 'LRC WIFI',</big> to fill Driver Assesment Form.</p><br/><p>Or visit <a href='" . $link_external . "/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to any Network,</big> to fill Driver Assesment Form.</p><br/><br/>Regards<br/>LRC 502 IT Department";
		}
			Mail::send([], [], function ($message) use ($email,$username,$body,$guid) {
  				$message->to($email,$username)
  						->from('lrc.station.502@gmail.com','LRC 502 IT')
    					->subject('Driver Assesment Form. Tikcet : ' . substr($guid,0,5))
	    				->setBody($body, 'text/html');
			});

			
			return true;
    	}catch(\Exception $e) {

    		Log::error($e);
    		return false;
    	}
    }


    public function handle(Urgence $urgence) {
		
		$emergency_id = $this->id;
		if($emergency_id == "") {
			return;
		}

		$emergency = Urgence::where('id',$emergency_id)->first();
		if(is_null($emergency)) {

			return;
		}

		if($emergency->type == 13)
			return;

		$people = array();
		$leader = $emergency->leader()->first();
		if(!is_null($leader))
			$people[] = $leader->mission_leader_id;
		$emts = $emergency->emts()->get();
		foreach ($emts as $emt) {
			$people[] = $emt->emt_id;
		}
		foreach ($people as $emt) {
			
			try{

				$leader_email = false;
				if(!is_null($leader)) {

					if( $leader->mission_leader_id == $emt ) {

						$leader_email = true;
					}
				}

				$survey = new Survey;
				$survey->guid = bin2hex(random_bytes(16));
				$survey->mission_id = $emergency_id;	
				$survey->user_id = $emt;

				//Check if survey exists 
				if(Survey::where('mission_id',$emergency_id)->where('user_id',$emt)->count() == 0) {

					$survey->save();
				} else {
					
					$survey = Survey::where('mission_id',$emergency_id)->where('user_id',$emt)->first();
				}

				$user = User::where('id',$emt)->first();
				$res = $this->sendEmail(trim($user->email),$user->name,$survey->guid,$leader_email);
				$counter = 0;
			
				while($res == false && $counter < 3) {
					$res = $this->sendEmail(trim($user->email),$user->name,$survey->guid, $leader_email);
					$counter += 1;
				}

				if($res == true) {
					$survey->sent = 1;
					$survey->save();
				}
			}catch(\Exception $e){

				Log::error($e);
			}
		}
	}
}
