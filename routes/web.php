<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('system_funcitonalities','SystemController@index');
Route::post('system_funcitonalities','SystemController@store');

Route::post('hospital_contacts','HospitalController@contacts');
Route::post('update_hospital_contacts','HospitalController@updateContacts');

Route::get('sendPending' , function() {
return "Disabled";
$pendingIds = App\Http\ORM\Survey::where('id','>',17078)->distinct('mission_id')->pluck('mission_id');

foreach( $pendingIds as $id ) {
	echo "Dispatching " . $id . "<br/>";

	App\Jobs\SendEmail::dispatch( App\Http\ORM\Urgence::where('id',$id)->first() )->onQueue('app_1');

}

return json_encode($pendingIds);

});


Route::get('image', function() {

// Mission documents are only shown on pages that require login
if(!Auth::check()) {
	abort(403);
}

$name = Illuminate\Support\Facades\Input::get('name');

// Refuse anything that could climb out of storage/app (e.g. ../../.env)
if(!is_string($name) || $name === '' || strpos($name, "\0") !== false || preg_match('#(^|[\\\\/])\.\.([\\\\/]|$)#', $name)) {
	abort(404);
}

$path = storage_path('app/' . $name);

if(!File::isFile($path)) {
	abort(404);
}

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
	

    return $response;
});


Route::get('first_aid_report','ReportController@createFirstAidReport');
Route::get('support_report','ReportController@createSupportReport');
Route::get('emt_support_report','ReportController@createEMTSupportReport');
Route::get('village_report','ReportController@createVillageReport');
Route::get('mission_type_report','ReportController@createMissionTypeReport');
Route::get('times_report','ReportController@createTimesReport');
Route::get('ambulance_report','ReportController@createAmbulanceReport');
Route::get('infecteous_disease_report','ReportController@createInfectousReport');
Route::get('detailed_report','ReportController@createDetailedReport');
Route::get('role_report','ReportController@roleReport');

Route::get('/emt_count','EmergencyReportsController@createEmtCountReport' );
Route::get('reports','ReportController@view');

Route::get('Checkups',function() {
return view('checkup_links');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/update_comment', 'UpdateCommentController@store');
Route::post('/update_to_from', 'UpdateCommentController@to_from');

Route::get('/ee','SummaryController@needcheckups');

Route::resource('/', 'MainController');

Route::resource('/hospitals', 'HospitalController');
Route::resource('/hos_location', 'HosLocation');

Route::resource('/summary', 'SummaryController');
Route::resource('/report', 'ReportController');

Route::resource('/add_fuel_coupon', 'AddFuelLog');
Route::resource('/edit_fuel_coupon', 'EditFuelLog');
Route::post('/delete_fuel_coupon', 'EditFuelLog@delete');
Route::resource('/driver_fuel_logs', 'FuelReportController');
//Route::get('/fuel_coupon_report', 'FuelReportController@generateReport');
Route::get('/generate_fuel_report', 'FuelReportController@generate_view');
Route::post('/generate_fuel_report', 'FuelReportController@generateReport');

Route::resource('/notification', 'NotificationController');

Route::resource('/add_village', 'AddVillageController');
Route::post('/get_villages', 'AddVillageController@get_villages');
Route::post('/update_village', 'AddVillageController@update_village');

Route::resource('/add_fuel_station', 'AddFuelStationController');

Route::resource('/addEMT', 'addEMT');

Route::resource('/addH', 'addH');

//Route::resource('/add_emergency', 'add_emergency');
Route::post('/add_emergency', 'add_emergency@store');
Route::get('/add_emergency', 'MainController@index');

Route::resource('edit_emergency','EditEmergencyController');
Route::post('check_emergency','EditEmergencyController@checkEmergency');
Route::resource('delete_emergency','DeleteEmergencyController');

Route::post('unlink_emergency_document','EditEmergencyController@unlink_doc');

Route::get('survey_statistics','SurveyController@view');
Route::post('search_survey','SurveyController@search_survey');
Route::get('view_survey','SurveyController@investigate');
Route::get('user_surveys','SurveyController@user_surveys');
Route::get('unfilled_user_surveys','SurveyController@unfilled_user_surveys');
Route::get('unfilledSurveyRatioReport','SurveyController@unfilledSurveyRatioReport');
Route::post('resetUnfilledSurveys','SurveyController@resetUnfilledSurveys');
Route::get('monthly_negative_comment','SurveyController@monthly_negative_comment');
Route::post('/monthly_negative_comment','SurveyController@monthly_negative_comment_report');

Route::get('monthly_positive_comment','SurveyController@monthly_positive_comment');
Route::post('/monthly_positive_comment','SurveyController@monthly_positive_comment_report');

Route::get('/allMaterial', 'MaterialController@index');
Route::get('/kioskMaterial', 'KioskController@index');
Route::get('/allMaterialMobile', function() {
	if(Auth::check() && 
		(
			Auth::user()->memberOf('Equipment Members')
				||
			Auth::user()->memberOf('Equipment Admin')
				||
			Auth::user()->memberOf('Equipment Head of Station')
		)
	) return view('material_mobile');
	return view('material_mobile');
});

Route::post('/allMaterial', 'MaterialController@edit');
Route::post('/kioskMaterial', 'KioskController@store');
Route::delete('/allMaterial', 'MaterialController@delete');

Route::post('add_edj_shifts', 'EdjController@store');
Route::get('add_edj_shifts', 'EdjController@index');
Route::get('edj_shifts_report', 'EdjController@generateExcel');
Route::get('edj_reports','EdjController@view');

Route::auth();

Route::get('/home', 'HomeController@index');

Route::resource('/build_maneuver', 'BuildManeuverController');
Route::get('/maneuvers', 'BuildManeuverController@view');
Route::get('/maneuver_results', 'BuildManeuverController@results');
Route::post('/duplicate_maneuver', 'BuildManeuverController@duplicate');
Route::resource('/maneuver','ManeuverController');


Route::resource('/register', 'RegisterController');
Route::get('all_register','RegisterController@summary');

Route::get('/car_checkup_all','car_checkup\CarCheckupController@getAllCarCheckups');
Route::post('/duplicate_car_checkup','car_checkup\CarCheckupController@duplicate');
Route::post('/delete_car_checkup','car_checkup\CarCheckupController@delete');
Route::get('/build_car_checkup','car_checkup\CarCheckupController@getCarCheckup');
Route::post('/build_car_checkup','car_checkup\CarCheckupController@buildCarCheckup');
Route::get('/car_checkup_result','car_checkup\CarCheckupController@carCheckupResults');
Route::post('/search_car_checkup_result','car_checkup\CarCheckupController@searchCarCheckupResults');
Route::post('/car_checkup_details','car_checkup\CarCheckupController@carCheckupDetails');
Route::post('/delete_car_checkup_details','car_checkup\CarCheckupController@deleteCarCheckupDetails');

Route::resource('/reset_pass','ResetPassController');

Route::get('/day_team_report' , 'EmergencyReportsController@createWeeklyReport'); 

Route::get('/emergency_call', 'CallController@index');
Route::get('/emergency_calls', 'CallController@search');
Route::post('/emergency_call', 'CallController@store');
Route::post('/emergency_calls', 'CallController@searchResults');
Route::post('/emergency_call_data', 'CallController@searchRecord');
Route::post('/delete_emergency_call', 'CallController@deleteRecord');

Route::get('/points', 'PointController@points');
Route::get('/manage_points', 'PointController@manage');
Route::get('/view_points', 'PointController@view');
Route::post('/user_points', 'PointController@user_points');
Route::post('/all_points', 'PointController@all_points');
Route::post('/update_point', 'PointController@update_point');
Route::post('/add_point', 'PointController@add_point');
Route::post('/delete_point', 'PointController@delete_point');
Route::get('/manage_point_actions', 'PointController@manage_actions');

Route::post('/list_point_actions', 'PointController@list_actions');
Route::post('/update_point_action', 'PointController@update_action');
Route::post('/add_point_action', 'PointController@add_action');
Route::get('/point_report', 'PointController@point_report');


Route::resource('/send_message','SendMessageController');
