<?php

namespace App\Jobs;

use App\Http\ORM\Group;
use App\Http\ORM\GroupMember;

use App\Http\ORM\Urgence;

use App\User;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;
use Mail;
use DateTime;


class EmergencyCountNotifier implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct(){
		
	}
 

    /**
     * Execute the job.
     *
     * @return void
     */

    private function sendEmail($data, $startDate , $endDate) {

    	try {

		$body = '<h3>Emergency Count Notification<h3/>' ;
		$body .= '<h5>Between ' . $startDate . ' And ' . $endDate . '<h5/>';
		$body .= '<br/><br/>Details : <ul>';
		foreach( $data as $d) {

			$body .= '<li>' . $d->name . ' (' . $d->count . ') </li>';
		} 

		$body .= '</ul><br/>Regards<br/>LRC 502 IT Department';
		
		Mail::send([], [], function ($message) use ($body) {

			foreach( GroupMember::where('group_id', Group::where('description' , 'Equipment Admin')->first()->id)->pluck('user_id')->toArray() as $id ) {

				$recepient = User::where( 'id' , $id )->first();
				$message = $message->to ( trim($recepient->email) , trim( $recepient->name ) );
			}
			
			$message->from('lrc.station.502@gmail.com','LRC 502 IT')
				->subject('Daily Emergency Count')	
   				->setBody($body, 'text/html');
		});
			
		return true;
    	}catch(\Exception $e) {

    		Log::error($e);
    		return false;
    	}
    }
	
	public function notifyAM(){
		Log::warning('sendAM');
		$startDate = new \DateTime();
		$startDate = $startDate->modify("-1 day")->format("Y-m-d") . " 18:00:00";
		$endDate = new \DateTime();
		$endDate = $endDate->format("Y-m-d") . " 07:59:59";
		$data = \App\Http\ORM\Urgence::leftJoin('mission_type' , 'urgence.type' , '=' , 'mission_type.id')
					->where('is_deleted',0)
					->whereBetween('urgence.created_at', [$startDate, $endDate])
					->groupBy('mission_type.name')
					->select('mission_type.name', \DB::raw('count(1) as count'))
					->get();

		$tries = 0;
		$res = false;
		while( $tries < 3 && $res == false ){
			$res = $this->sendEmail($data, $startDate, $endDate);
			$tries = $tries + 1;
		}		
	}

	public function notifyPM(){
		Log::warning('sendPM');
		$startDate = new \DateTime();
		$startDate = $startDate->format("Y-m-d") . " 08:00:00";
		$endDate = new \DateTime();
		$endDate = $endDate->format("Y-m-d") . " 17:59:59";
		$data = \App\Http\ORM\Urgence::leftJoin('mission_type' , 'urgence.type' , '=' , 'mission_type.id')
					->where('is_deleted',0)
					->whereBetween('urgence.created_at', [$startDate, $endDate])
					->groupBy('mission_type.name')
					->select('mission_type.name', \DB::raw('count(1) as count'))
					->get();

		$tries = 0;
		$res = false;
		while( $tries < 3 && $res == false ){
			$res = $this->sendEmail($data, $startDate, $endDate);
			$tries = $tries + 1;
		}		
	}	

}
