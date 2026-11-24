<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use File;
use Auth;

use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\ambulance as Ambulance;
use App\Http\ORM\Urgence;
use App\Http\ORM\Nationalities;
use App\Http\ORM\mission_subtype;
use App\Http\ORM\mission_type;
use App\Http\ORM\hospital;
use App\Http\ORM\station;
use App\Http\ORM\location;
use App\Http\ORM\UrgenceChangeLog as CL;

use App\Http\ORM\MissionDriver;
use App\Http\ORM\MissionLeader;
use App\Http\ORM\MissionEMT;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

class DeleteEmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


 
    public function store(){

        $s = json_decode(Request::get('id'));
        //Store
        File::append(storage_path('logs/Delete_Emergency_Log.txt'),json_encode($s) . "\n");


        $urgs = array();

        try{

                if( Auth::check() && 
				( Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id) || 
				Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Chef Equipe')->first()->id))){

            \DB::transaction(function() use ($s) {

                $urg = Urgence::where('id',$s)->first();

			$urg->is_deleted = 1;
			$urg->save();

		        $c = new CL;
	     		$c->field = "Delete Urgence";
        		$c->old_value = "";
        		$c->new_value = "";
        		$c->user_id = Auth::user()->id;
			$c->save();

            });
	$response = new Response;
	$response->Status = 1;
	return json_encode($response);

}else {
	$response = new Response;
	$response->Status = 0;
	$response->Message = "Insufficient Access Level"; 
	return json_encode($response);
}

        }catch(\Exception $e){
            $response = New Response;
            $response->Status = 0;
	    $response->Message = "An Error Has Occured. Please Consult IT";
            Log::error($e);
            return json_encode($response);
        }
        $response = New Response;
        $response->Status = 1;
        return json_encode($response);
        
    }
}
