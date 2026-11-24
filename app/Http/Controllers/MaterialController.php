<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use app\Http\Requests;
use Auth;
use App\Responses\Response;
use File;


use App\Http\ORM\Material as AllMaterial;
use App\Http\ORM\MaterialLog;
use App\Http\ORM\MaterialChangeLog as CL;

use Illuminate\Support\Facades\Log;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function verfiy_access() {
//return true;
	$head_of_station = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id);
	$material_admin	 = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Equipment Admin')->first()->id);
	$material_member = Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Equipment Members')->first()->id);

	return $head_of_station || $material_admin || $material_member;
    }

	private function cl($amount,$user,$material_id) {
		$cl = new MaterialLog();
		$cl->user_id = $user;
		$cl->amount = $amount;
		$cl->material_id = $material_id;
		return $cl;
	}	

    public function index()
    {

	if(!$this->verfiy_access())
		return view('errors.404');

        date_default_timezone_set("Asia/Beirut");

        $materials = AllMaterial::select('comment', 'id','name as description','amount as count','criticalAmount as criticalCount')->where('is_deleted',0)->get();
        foreach($materials as $material){
			$divide_by = $material->criticalAmount;
			if($divide_by == 0) {
				$divide_by = 1;
			}
            $material->percentage = number_format((($material->count / $divide_by) * 100), 2, '.', '') . " %";
        }
        return view('material',compact('materials'));
    }

    public function delete() {

	if( !$this->verfiy_access() ) {

	    $resp = new Response;
            $resp->Response = 0;
            $resp->Message = "Access Denied";
            return json_encode($resp);
	}

	try {

		$data = Request::get('form');

		File::append(storage_path('logs/Delete_Material_Log.txt'),"User : " . Auth::user()->name . "\n");
		File::append(storage_path('logs/Delete_Material_Log.txt'),"At : " . date("D M d, Y G:i") . "\n");
		File::append(storage_path('logs/Delete_Material_Log.txt'),json_encode($data));
		File::append(storage_path('logs/Delete_Material_Log.txt'),"\n");

		$mat = AllMaterial::where('id',$data)->first();
		if($mat == null) {

			$resp = new Response;
            		$resp->Status = 0;
			$resp->Message = "The material you are trying to delete, does not exist";
            		return json_encode($resp);
		}else{
			\DB::transaction( function () use($mat) {

				$cl = new CL();
				$cl->material_id = $mat->id;
				$cl->user_id = Auth::user()->id;
				$cl->old_value = "";
				$cl->new_value = "";
				$cl->field = "Delete Material";
				$cl->save();

				$mat->is_deleted = 1;
				$mat->save();
			});
		}

	        $resp = new Response;
		$resp->Status = 1;
       		return json_encode($resp);


	} catch (\Exception $e) {

		Log::error($e);
        	$resp = new Response;
        	$resp->Status = 0;
		$resp->Message = "An Error has Occured, please contact IT";
        	return json_encode($resp);
	}
    }

    public function edit(){

	if(!$this->verfiy_access()) {

	    $resp = new Response;
            $resp->Response = 0;
            $resp->Message = "Access Denied";
            return json_encode($resp);
	}
try {
	$data = Request::get('form');

	File::append(storage_path('logs/Edit_Material_Log.txt'),"User : " . Auth::user()->name . "\n");
	File::append(storage_path('logs/Edit_Material_Log.txt'),"At : " . date("D M d, Y G:i") . "\n");
	File::append(storage_path('logs/Edit_Material_Log.txt'),json_encode($data));
	File::append(storage_path('logs/Edit_Material_Log.txt'),"\n");

	if($data['id'] == null) {
		
		$mat = new AllMaterial();
		$mat->name = $data['description'];
		$mat->amount = $data['count'];
		$mat->criticalAmount = $data['criticalCount'];
		$mat->comment = $data['comment'];
		$mat->save();

		$resp = new Response;
            	$resp->Status = 1;
            	$resp->id = $mat->id;
            	return json_encode($resp);
	}else{

		$mat = AllMaterial::where('id',$data['id'])->first();
		if($mat == null) {

			$resp = new Response;
            		$resp->Status = 0;
			$resp->Message = "The material you are trying to edit, does not exist";
            		return json_encode($resp);
		}else{
			\DB::transaction( function () use($mat,$data) {

				if( trim($mat->name) != trim($data['description']) ) {

					$cl = new CL();
					$cl->material_id = $mat->id;
					$cl->user_id = Auth::user()->id;
					$cl->old_value = $mat->name;
					$cl->new_value = $data['description'];
					$cl->field = "Name";
					$cl->save();

					$mat->name = $data['description'];
					$mat->save();
				}

				if( trim($mat->amount ) != trim($data['count']) ) {

					$cl = new CL();
					$cl->material_id = $mat->id;
					$cl->user_id = Auth::user()->id;
					$cl->old_value = $mat->amount ;
					$cl->new_value = $data['count'];
					$cl->field = "Count";
					$cl->save();

					$this->cl($mat->amount - $data['count'] ,Auth::user()->id,$data['id'])->save();

					$mat->amount = $data['count'];
					$mat->save();
				}

				if( trim($mat->criticalAmount ) != trim($data['criticalCount']) ) {

					$cl = new CL();
					$cl->material_id = $mat->id;
					$cl->user_id = Auth::user()->id;
					$cl->old_value = $mat->criticalAmount ;
					$cl->new_value = $data['criticalCount'];
					$cl->field = "Critical Count";
					$cl->save();

					$mat->criticalAmount = $data['criticalCount'];
					$mat->save();
				}

				if( trim($mat->comment) != trim($data['comment']) ) {

					$cl = new CL();
					$cl->material_id = $mat->id;
					$cl->user_id = Auth::user()->id;
					$cl->old_value = $mat->comment;
					$cl->new_value = $data['comment'];
					$cl->field = "Comment";
					$cl->save();

					$mat->comment = $data['comment'];
					$mat->save();
				}

			});

		        $resp = new Response;
       			$resp->Status = 1;
			$resp->id = $data['id'];
        		return json_encode($resp);
		}
	}

}catch(\Exception $e) {
	Log::error($e);
        $resp = new Response;
        $resp->Status = 0;
	$resp->Message = "An Error has Occured, please contact IT";
        return json_encode($resp);

}
    }
}
