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

class KioskController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	private function verfiy_access()
	{

		return Auth::user()->memberOf('Equipment Members');
	}

	public function index()
	{

		if (!$this->verfiy_access())
			return view('errors.404');

		$materials = AllMaterial::select('id', 'kiosk', 'kiosk_count', 'name')
			->where('is_deleted', 0)->get();
		return view('kiosk_material', compact('materials'));
	}

	public function store()
	{

		if (!$this->verfiy_access()) {

			$resp = new Response;
			$resp->Response = 0;
			$resp->Message = "Access Denied";
			return json_encode($resp);
		}
		try {
			$data = Request::get('form');

			File::append(storage_path('logs/Edit_Kiosk_Material_Log.txt'), "User : " . Auth::user()->name . "\n");
			File::append(storage_path('logs/Edit_Kiosk_Material_Log.txt'), "At : " . date("D M d, Y G:i") . "\n");
			File::append(storage_path('logs/Edit_Kiosk_Material_Log.txt'), json_encode($data));
			File::append(storage_path('logs/Edit_Kiosk_Material_Log.txt'), "\n");

			$mat = AllMaterial::where('id', $data['id'])->first();
			if ($mat == null) {

				$resp = new Response;
				$resp->Status = 0;
				$resp->Message = "The material you are trying to edit, does not exist";
				return json_encode($resp);
			} else {
				\DB::transaction(function () use ($mat, $data) {


					if (trim($mat->kiosk_count) != trim($data['kiosk_count'])) {

						$cl = new CL();
						$cl->material_id = $mat->id;
						$cl->user_id = Auth::user()->id;
						$cl->old_value = $mat->kiosk_count;
						$cl->new_value = $data['kiosk_count'];
						$cl->field = "Kiosk Count";
						$cl->save();

						$mat->kiosk_count = $data['kiosk_count'];
						$mat->save();
					}

					if ($mat->kiosk != ($data['kiosk'] == "true" ? 1 : 0)) {

						$cl = new CL();
						$cl->material_id = $mat->id;
						$cl->user_id = Auth::user()->id;
						$cl->old_value = $mat->kiosk;
						$cl->new_value = ($data['kiosk'] == "true" ? 1 : 0);
						$cl->field = "Kiosk";
						$cl->save();

						$mat->kiosk = ($data['kiosk'] == "true" ? 1 : 0);
						$mat->save();
					}
				});

				$resp = new Response;
				$resp->Status = 1;
				$resp->id = $data['id'];
				return json_encode($resp);
			}
		} catch (\Exception $e) {
			Log::error($e);
			$resp = new Response;
			$resp->Status = 0;
			$resp->Message = "An Error has Occured, please contact IT";
			return json_encode($resp);
		}
	}
}
