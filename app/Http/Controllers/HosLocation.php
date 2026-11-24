<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\ORM\hospital as Hospital;
use App\Http\ORM\PhoneNumber;
use App\Http\ORM\district;
use App\Http\ORM\station;

class HosLocation extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hos_id = isset($_GET['id']) ? $_GET['id'] : null;
        if(!is_numeric($hos_id)) {
            return view('errors.404');
        }

        $hospital = Hospital::where('id',$hos_id)->first();
        if(is_null($hospital)) {
            return view('errors.404');
        }

        $hospital_name = $hospital->code . " (" . $hospital->name . ")";//34.2550346,35.664844
        $lon = is_null($hospital->longitude) ? 35.664844 : $hospital->longitude;
        $lat = is_null($hospital->latitude) ? 34.2550346 : $hospital->latitude;//$hospital->latitude;

        // Contacts removed from the Hospitals page are only flagged is_deleted, so skip them here
        // (older rows may have is_deleted = NULL, which still means "not deleted")
        $phones = PhoneNumber::select('phone')->where('hospital_id',$hos_id)
                    ->where(function($query) {
                        $query->where('is_deleted',0)->orWhereNull('is_deleted');
                    })->get();

        $district = district::select('name')->where('id',substr($hospital->station,0,1) . "00")->first();
        $district = is_null($district) ? "N/A" : $district->name;
        $station = station::select('name')->where('id',$hospital->station)->first();
        $station = is_null($station) ? "N/A" : $station->name;
        // if(!is_null($phones) && count($phones) > 0){
        //     if(count($phones) > 1){
        //                 $description .= "<br/>\t&bull;Phone Numbers : ";
        //     }else{
        //                 $description .= "<br/>\t&bull;Phone Number : ";
        //     }
        //     foreach ($phones as $phone) {
        //         $description .= "<br/>\t\t&bull;" . $phone->phone;
        //     }
        // }
        return view('hos_location',compact('hospital_name','lon','lat','phones','station','district'));
    }
}
