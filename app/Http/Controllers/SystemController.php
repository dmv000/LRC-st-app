<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;

use App\Responses\Response;
use App\Http\Controllers\PrivateFunct;

use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function verify_access() {

        return Auth::user()->memberOf('IT Members');
    }

    public function index() {
		
		if( !$this->verify_access() ){
			unauthorized('Get Add_Fuel_Log View',Auth::user());
			return view('errors.404');
		}
		return view('system');
		
	}
	
	public function store() {
		
		$response = new Response;
		$response->Status = 0;
		
		if( !$this->verify_access() ){
			unauthorized('Get Add_Fuel_Log View',Auth::user());
			$response->Message = "Unauthorized Access. This Action has been monitored, and will be reported";
			return json_encode($response);
		}
		
		$action = Request::get('action');
		$type = Request::get('type');
		
		try {
			
			$command = "systemctl";
			
			if( $action == "check" ){
				$command .= " status";
			} else if( $action == "restart" ){
				$command .= " restart";
			} else if ( $type == "resetUsers") {
				$p = new PrivateFunct;
				$p->sync_users();
				$response->Status = 1;
				$response->Message = "Users Reset";
				return json_encode($response);
			}
			
			if( $type == "supervisord" ){
				$command .= " supervisord";
			}
			
			if( $action == "restart" ){
				if( $type == "supervisord" ){
					shell_exec('sudo /var/www/html/stat_app/storage/app/public/scripts/restartSupervisord.sh');
					$command = 'systemctl status supervisord';
				}
			}
			
			Log::warning('SystemController ' . $command);
			$response->Status = 1;
			$response->Message = shell_exec($command);
			return json_encode($response);
			
		} catch(\Exception $e){
			Log::error($e);
			$response->Message = "An Error Occured. Please Consult IT";
			return json_encode($response);
		}
		
	}


}
