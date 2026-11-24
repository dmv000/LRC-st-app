<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use App\User as EMT;
use Auth;

use App\Http\ORM\Comment;

use App\Http\ORM\News;

use App\Responses\Response;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {

	$id = Request::get('id');

	if($id == "") {

		return view('errors.404');		
	}

	$news = News::where('id',$id)->first();

	if(is_null($news)) {

		return view('errors.404');
	}

	if($news->user_id != Auth::user()->id) {

		return view('errors.404');
	}

	$news->is_read = 1;
	$news->read_at = date("Y-m-d H:i:s");
	$news->save();
	
	return redirect($news->link);

    }

	public function store() {


		try{

			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
			$data = json_decode(Request::get('form'));
	
			$ids = array();
			$emts  = EMT::get();
			foreach($emts as $emt) {

				if($emt->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Secretariat Members')->first()->id)) {

	
					$ids[] =  $emt;
				}
			}



			foreach($ids as $id) {

				$news = new News;
				$news->description = 'Add New Village Here<br/>??? ???? ?? ??????? ?? ??? ??????';
				$news->link = '\add_village';
				$news->user_id = $id->id;
				$news->save();
			}

		} catch (\Exception $e) {

			Log::error($e);
			$reponse = new Response;
			$reponse->Status = 0;
			$response->Message = "An Error Has Occured. Please Cosult IT";
			return json_encode($response);
		}
	}

	public function test() {

return view('add_notification');
}

}
