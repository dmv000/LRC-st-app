<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use File;
use Auth;

use App\Http\ORM\comment;
use App\Responses\Response;
use App\User as EMT;
use App\Http\ORM\ambulance as Ambulance;
use App\Http\ORM\fuel_stations as FuelStations;
use App\Http\ORM\fuel_types as FuelTypes;
use App\Http\ORM\FuelCoupon as FC;
use App\Http\ORM\FuelCouponChangeLog as FCCL;
use App\Http\ORM\Event;
use App\Http\ORM\EventType;

class EditFuelLog extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		try {
			$id = Input::get('Id');
			if ($id == "") {
				return view('errors.404');
			}
			$page_title = "Edit Fuel Coupon/تعديل قسيمة فيول";

			$drivers = EMT::select('name', 'nickname', 'id')
				->where('driver', 1)
				->where('is_deleted', 0)
				->get();

			foreach ($drivers as $driver) {
				if ($driver->nickname == "") {
					$driver->nickname = $driver->name;
				} else {
					$driver->nickname = $driver->nickname . " (" . $driver->name . ")";
				}
			}

			$stations = FuelStations::select('id', 'name')->get();

			$cars = Ambulance::select('id', 'car_number')
				->where('is_deleted', 0)
				->get();



			$fuel_types = FuelTypes::select('id', 'description')->get();
			$record = FC::select(
				'id',
				'car_id',
				'driver_id',
				'army',
				'date',
				'fuel_station_id',
				'fuel_type_id',
				'coupon_number',
				'litre',
				'price',
				'kms'
			)->where('id', $id)->first();
			if (is_null($record))
				return view('errors.404');

			$tempDate = explode("-", $record->date);
			$date = $tempDate[1] . "/" . $tempDate[2] . "/" . $tempDate[0];
			return view('edit_fuel', compact('record', 'fuel_types', 'stations', 'page_title', 'drivers', 'cars', 'date', 'fuel_types'));
		} catch (\Exception $e) {
			Log::warning($e);
			return view('errors.503');
		}
	}

	private function cl($field, $old, $new, $coupon_id)
	{

		$c = new FCCL;
		$c->field = $field;
		$c->old_value = $old;
		$c->new_value = $new;
		$c->edited_by = Auth::user()->id;
		$c->fuel_coupon_id = $coupon_id;
		return $c;
	}

	public function store()
	{

		$s = json_decode(Request::get('form'));
		File::append(storage_path('logs/Edit_Fuel_Log.txt'), json_encode($s) . "\n");

		try {

			\DB::transaction(function () use ($s) {

				$log = FC::where('id', $s->id)->first();
				if (is_null($log))
					throw new \Exception('Wrong Fuel Log ID while editing, ID: ' + $s->id + '.User: ' . Auth::user()->nickname);

				if (isset($s->car_number)) {

					if ($log->car_id != $s->car_number) {

						$this->cl('Car Number', $log->car_id, $s->car_number, $s->id)->save();
						$log->car_id = $s->car_number;
					}
				} else {

					throw new \Exception('Missing Car Number on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->driver)) {

					if ($log->driver_id != $s->driver) {

						$this->cl('Driver', $log->driver_id, $s->driver)->save();
						$log->driver_id = $s->driver;
					}
				} else {

					throw new \Exception('Missing Driver on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->date)) {
					$tempDate = explode("/", $s->date);
					$s->date = $tempDate[2] . "-" . $tempDate[0] . "-" . $tempDate[1];
					if ($log->date != $s->date) {

						$this->cl('Date', $log->date, $s->date, $s->id)->save();
						$log->date = $s->date;
					}
				} else {

					throw new \Exception('Missing Date on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->fuel_station)) {

					if ($log->fuel_station_id != $s->fuel_station) {

						$this->cl('Fuel Station', $log->fuel_station_id, $s->fuel_station, $s->id)->save();
						$log->fuel_station_id = $s->fuel_station;
					}
				} else {

					throw new \Exception('Missing Fuel Station on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->fuel_type)) {

					if ($log->fuel_type_id != $s->fuel_type) {

						$this->cl('Fuel Type', $log->fuel_type_id, $s->fuel_type, $s->id)->save();
						$log->fuel_type_id = $s->fuel_type;
					}
				} else {

					throw new \Exception('Missing Fuel Type on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->number) && !empty($s->number)) {

					if ($log->coupon_number != $s->number) {
						$count = FC::where('coupon_number', (int)$s->number)->where('is_deleted', 0)->get()->count();

						if ($count > 0) {
							$response = new Response;
							$response->Status = 0;
							$response->Message = "Coupon Number Already Exists";
							Log::warning(json_encode($response));
							throw new \Exception($response->Message);
						}

						try {

							$this->cl('Coupon Number', $log->coupon_number, $s->number, $s->id)->save();
							$log->coupon_number = $s->number;
						} catch (\Exception $e) {

							throw new \Exception('Wrong Coupon Number on Edit Fuel Log for User ' . Auth::user()->nickname);
						}
					}
				}/*else{

				throw new \Exception('Missing Coupon Number on Edit Fuel Log for User ' . Auth::user()->nickname);
			}*/

				if (isset($s->litres)) {

					if ($log->litre != $s->litres) {

						try {

							$this->cl('Litre', $log->litre, $s->litres, $s->id)->save();
							$log->litre = $s->litres;
						} catch (\Exception $e) {

							throw new \Exception('Wrong Litre on Edit Fuel Log for User ' . Auth::user()->nickname);
						}
					}
				} else {

					throw new \Exception('Missing Litre on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->price)) {

					if ($log->price != $s->price) {

						try {

							$this->cl('Price', $log->price, $s->price, $s->id)->save();
							$log->price = $s->price;
						} catch (\Exception $e) {

							Log::error($e);
							throw new \Exception('Wrong Price on Edit Fuel Log for User ' . Auth::user()->nickname);
						}
					}
				} else {

					throw new \Exception('Missing Price on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->kms)) {

					if ($log->kms != $s->kms) {

						try {

							$this->cl('Kilometrage', $log->kms, $s->kms, $s->id)->save();
							$log->kms = $s->kms;
						} catch (\Exception $e) {

							throw new \Exception('Wrong Kilometrage on Edit Fuel Log for User ' . Auth::user()->nickname);
						}
					}
				} else {

					throw new \Exception('Missing Kilometrage on Edit Fuel Log for User ' . Auth::user()->nickname);
				}

				if (isset($s->army))
					$s->army = 1;
				else
					$s->army = 0;
				if ($s->army != $log->army) {

					$this->cl('Army', $log->army, $s->army, $s->id)->save();
					$log->army = $s->army;
				}
				$log->save();

				$event = new Event;
				$event->user_id = Auth::user()->id;
				$event->event_type = EventType::where('description', 'Edit Fuel Coupon')->first()->id;
				$event->save();
			});

			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
		} catch (\Exception $e) {

			Log::error($e);
			$response = new Response;
			$response->Status = 0;
			$response->Message = "An Error Has Occured. Please Contact IT";
			return json_encode($response);
		}

		$response = new Response;
		$response->Status = 1;
		return json_encode($response);
	}




	public function delete()
	{

		$s = json_decode(Request::get('id'));
		File::append(storage_path('logs/Delete_Fuel_Log.txt'), json_encode($s) . "\n");

		try {

			\DB::transaction(function () use ($s) {

				$log = FC::where('id', $s)->first();
				if (is_null($log))
					throw new \Exception('Wrong Fuel Log ID while deleting, ID: ' + $s + '.User: ' . Auth::user()->nickname);
				if (Auth::check() && (Auth::user()->groups()->get()->contains('group_id', \App\Http\ORM\Group::where('description', 'Drivers Admin')->first()->id)
					||
					Auth::user()->groups()->get()->contains('group_id', \App\Http\ORM\Group::where('description', 'Head of Station')->first()->id))) {
					$log->is_deleted = 1;
				} else {
					throw new \Exception('Insufficient privileges');
				}
				$log->save();

				$event = new Event;
				$event->user_id = Auth::user()->id;
				$event->event_type = EventType::where('description', 'Edit Fuel Coupon')->first()->id;
				$event->save();
			});

			$response = new Response;
			$response->Status = 1;
			return json_encode($response);
		} catch (\Exception $e) {

			Log::error($e);
			$response = new Response;
			$response->Status = 0;
			$response->Message = "An Error Has Occured. Please Contact IT";
			return json_encode($response);
		}
		//
		$response = new Response;
		$response->Status = 1;
		return json_encode($response);
	}
}
