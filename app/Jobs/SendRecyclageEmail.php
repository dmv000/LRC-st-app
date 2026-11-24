<?php

namespace App\Jobs;

use App\Http\ORM\ShiftEMT;
use App\Http\ORM\ShiftParticipant;
use App\Http\ORM\Group;
use App\Http\ORM\GroupMember;

use App\Http\ORM\Register;
use App\Http\ORM\Survey;

use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;
use Mail;


class SendRecyclageEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $id;

    public function __construct(Register $register)
    {
        $this->id = $register->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    private function sendEmail($register) {

    	try {

		$body = '<h3>Dear Sir&nbsp;</h3><br/><br/>A new Recyclage was completed, on "' . $register->register_date ;
		$body .= '", Description : <br/>' . $register->skills;
		$body .= '<br/>Participants : <ul>';
		foreach( ShiftEMT::where('is_deleted' , 0)->where('shift_id' , $register->id)->get() as $se) {

			$body .= '<li>' . User::where('id' , $se->user_id)->first()->nickname . '</li>';
		} 

		foreach( ShiftParticipant::where('is_deleted' , 0)->where('shift_id' , $register->id)->get() as $se) {

			$body .= '<li>' . User::where('id' , $se->user_id)->first()->nickname . '</li>';
		} 
		$body .= '</ul><br/>Regards<br/>LRC 502 IT Department';
		
		Mail::send([], [], function ($message) use ($body) {

			/*foreach( GroupMember::where('group_id', Group::where('description' , 'Technique Admin')->first()->id)->pluck('user_id')->toArray() as $id ) {

				$recepient = User::where( 'id' , $id )->first();
				$message = $message->to ( trim($recepient->email) , trim( $recepient->name ) );
			}*/
			$message = $message->to ( env('TECHINQUE_ADMIN') , 'Techinque Admin' );

			$message->from('lrc.station.502@gmail.com','LRC 502 IT')
				->subject('Recyclage Notification')	
   				->setBody($body, 'text/html');
		});
			
		return true;
    	}catch(\Exception $e) {

    		Log::error($e);
    		return false;
    	}
    }


    public function handle(Register $register) {

		$id = $this->id;
		if($id == "") {
			return;
		}

		$register = Register::where('id',$id)->first();
		if(is_null($register)) {

			return;
		}

		if($register->finished != 1)
			return;

		$res = $this->sendEmail($register);
	}
}
