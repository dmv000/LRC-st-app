<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use Auth;
use File;

use App\Responses\Response;
use App\Http\ORM\BloodBanks

use Illuminate\Support\Facades\Log;

class BloodBanksController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function view() {

	$page_title = "Blood Banks";
	$blood_banks = BloodBanks::get();
	return view('blood_banks',compact($page_title,$blood_banks));
    }
}
