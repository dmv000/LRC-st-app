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

    private function sendEmail($email,$username,$guid) {

    	Log::debug('Send Email Request : ' . $email . ' ' . $username . ' ' . $guid);
    	try {

			$body = '<h3>Dear ' . $username . "&nbsp;</h3><br/><br/><p>Please visit <a href='http://survey.lrc502.local/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to 'LRC WIFI',</big> to fill Driver Assesment Form.</p><br/><p>Or visit <a href='http://lrc502.us.to:180/driver_survey?id=" . $guid . "'>this link</a>, <big>while connected to any Network,</big> to fill Driver Assesment Form.</p><br/><br/>Regards<br/>LRC 502 IT Department";
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


    public function handle(Urgence $urgence)
    {
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
			$survey = new Survey;
			$survey->guid = bin2hex(random_bytes(16));
			$survey->mission_id = $emergency_id;	
			$survey->user_id = $emt;
//Check if survey exists 
if(Survey::where('mission_id',$emergency_id)->where('user_id',$emt)->count() == 0) {

			$survey->save();
}else{
			$survey->guid = Survey::where('mission_id',$emergency_id)->where('user_id',$emt)->first()->guid;
}

			$user = User::where('id',$emt)->first();
			$res = $this->sendEmail($user->email,$user->name,$survey->guid);
			$counter = 0;
			while($res == false && $counter < 3) {
				$res = $this->sendEmail($user->email,$user->name,$survey->guid);
				$counter += 1;
			}
	//		Log::warning('Mail Sent ' . $res);
			//Send notification
}catch(\Exception $e){
Log::error($e);
}
		}
    }
}
