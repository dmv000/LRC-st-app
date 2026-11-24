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

class AddVillageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function get_villages()
	{
		try {

			$start = Request::get("jtStartIndex");
			$limit = Request::get("jtPageSize");
			$station = Request::get("station");
			$village = Request::get("village");

			$selector = Village::select('id', 'station', 'village');
			if (isset($station) && is_numeric($station) && $station > 0) {
				$selector = $selector->where('station', $station);
			}
			if (isset($village)) {
				$selector = $selector->where(\DB::raw('lower(village)'), 'like', '%' . strtolower($village) . '%');
			}

			$villages = $selector->skip($start)->take($limit)->get();
			$village_count = $selector->count();

			$response = array();
			$response['Result'] = "OK";
			$response['TotalRecordCount'] = $village_count;
			$response['Records'] = $villages;
			return json_encode($response);
		} catch (\Exception $e) {
			Log::error($e);
			$response = array();
			$response['Result'] = "OK";
			$response['Message'] = "Failed Getting Villages";
			return json_encode($response);
		}
	}

	public function store()
	{

		if (!$this->verify_access()) {
			//Log 
			unauthorized('Add Village', Auth::user());
			$response['Result'] = "ERROR";
			$response['Message'] = "Unauthorized access";
			return json_encode($response);
		}

		$station = Request::get("station");
		$village = Request::get("village");

		try {

			$record = \DB::transaction(function () use ($station, $village) {
				$log = new Village;

				if (isset($village) && $village != null && $village != "") {

					$log->village = $village;
				} else {

					$response->Status = 0;
					$response->Message = "Village Name Error";
					Log::warning(json_encode($response));
					return json_encode($response);
				}

				if (isset($station) && $station != "-1") {

					$log->station = $station;
				} else {

					$response->Status = 0;
					$response->Message = "Station Error";
					Log::warning(json_encode($response));
					throw new \Exception($reponse->Message);
				}

				$log->save();

				$event = new Event;
				$event->user_id = Auth::user()->id;
				$event->event_type = EventType::where('description', 'Add Village')->first()->id;
				$event->save();

				return $log;
			});

			$response['Result'] = "OK";
			$response['Record'] = $record;
			return json_encode($response);
		} catch (\Exception $e) {
			Log::error($e);
			$response['Result'] = "ERROR";
			$response['Message'] = $e->getMessage();
			return json_encode($response);
		}
	}

	public function update_village()
	{

		if (!$this->verify_access()) {
			//Log 
			unauthorized('Update Village', Auth::user());
			$response['Result'] = "ERROR";
			$response['Message'] = "Unauthorized access";
			return json_encode($response);
		}

		$id = Request::get("id");
		$station = Request::get("station");
		$village = Request::get("village");

		try {

			$record = \DB::transaction(function () use ($id, $station, $village) {

				$log = Village::where('id', $id)->first();

				if (is_null($log))
					throw new \Exception('Wrong Village Id while editing, ID: ' + $id + '.User: ' + Auth::user()->nickname);

				if (isset($village) && $village != null && $village != "") {

					if ($village != $log->village) {
						$ev = new Event();
						$ev->create('Edit Village', "Change Village from [{$log->village}] to [{$village}]");
						$ev->save();
						$log->village = $village;
						$log->save();
					}
				}

				if (isset($station) && $station != "-1") {

					if ($station != $log->station) {
						$ev = new Event();
						$ev->create('Edit Village', "Change Station from [{$log->station}] to [{$station}]");
						$ev->save();
						$log->station = $station;
						$log->save();
					}
				}
				return $log;
			});

			$response['Result'] = "OK";
			$response['Record'] = $record;
			return json_encode($response);
		} catch (\Exception $e) {
			Log::error($e);
			$response['Result'] = "ERROR";
			$response['Message'] = $e->getMessage();
			return json_encode($response);
		}
	}


	public function verify_access()
	{
		return Auth::user()->memberOf('Secretariat Members') || Auth::user()->memberOf('Chef Equipe');
		//This page is visible to all drivers
		//return Auth::user()->driver;
	}

	public function index()
	{

		if (!$this->verify_access()) {
			//Log 
			unauthorized('Get Add_Village_Log View', Auth::user());
			return view('errors.404');
		}

		$page_title = "Add Village";
		$stations = Station::select('name', 'id')->groupBy('id')
			->get();


		return view('add_village', compact('stations', 'page_title'));
	}
}
