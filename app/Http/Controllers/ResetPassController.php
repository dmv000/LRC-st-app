<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\location as Village;
use App\Http\ORM\station as Station;

use App\Http\ORM\Event;
use App\Http\ORM\EventType;

use Illuminate\Support\Facades\Log;

class ResetPassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store() {

	if(!$this->verify_access()) {
            //Log 
            $response = new Response;		
		$response->Status = 0;
		$response->Message = "Unauthorized Access";
		return json_encode($response);
        }


	$s = Request::get('form');
	$response = new Response;

	try {

		$post_data = array(
			"vuser" => $s['vuser'],
			"yuser" => $s['yuser'],
			"ypass" => $s['ypass']
		);
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, env('RESET_APP_URL'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$output = curl_exec($ch);


		if( $output === false ) {
			$response->Status = 0;
			Log::warning("Reset Password failed with Erorr [".curl_error($ch)."] and No [".curl_errno($ch)."]");
			$response->Message = "Password reset failed. Please contact IT";
		} else {
			$response->Status = 1;
			$response->Message = $output;
		}
		curl_close($ch);

		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->details = "For user [" . $s['vuser'] . "]";
		$event->event_type = EventType::where('description','Reset Password')->first()->id;
		$event->save();
		return json_encode($response);
	}catch(\Exception $e){
		Log::error($e);
		$response = new Response;
		$response->Status = 0;
		$response->Message = "Password reset failed. Please contact IT";
		return json_encode($response);
	}

    }

    
    public function verify_access() {
	return Auth::user()->memberOf('Chef Equipe');
        //This page is visible to all drivers
        //return Auth::user()->driver;
    }

	public function index(){
	
        if(!$this->verify_access()) {
            //Log 
            unauthorized('Get Reset User View',Auth::user());
            return view('errors.404');
        }

		$page_title = "Reset User Password";
		return view('reset_user');
	
	}

}
