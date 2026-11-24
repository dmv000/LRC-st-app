<?php

namespace App\Http\Controllers;


use Request;
use App\Http\ORM\hospital;
use DateTime;

class addH extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function AddEntryLog($array){

        $count = count($array);
        //$failCount = 0;
        //$errorMessage = {};
        //$errorMessage->Response = 1;

		foreach ($array as $object) {
            if(is_null($object)){
                //$failCount += 1;
                //$errorMessage->Response = 0;
                //$errorMessage->Message .= AllStrings::$object_projection . "\n";
                continue;
            }

            $obj = (object) $object;



            if( true || (isset($obj->Event_Id) &&
                isset($obj->Timestamp) &&
                isset($obj->Time_user) &&
				isset($obj->Driver_user_Id) &&
                isset($obj->Controller_user_Id) &&
                isset($obj->Exit_record) &&
                isset($obj->Truck_Id) &&
                isset($obj->Tank_Id) &&
                isset($obj->Truck_volume) &&
				isset($obj->Payment_voucher_number) &&
				isset($obj->Comment) &&
				isset($obj->Data_edited)
)
				){










                    $log = New hospital;
                    $log->station = $obj->station;
                    $log->name = $obj->name;
                    $log->code = $obj->code;
                    $log->save();




            }
            else {
                    //$failCount += 1;
                    //$errorMessage->Response = 0;
                    //$errorMessage->Message .= AllStrings::$cen_info_missing . (isset($obj->Event_Id) ? $obj->Event_Id : '') . "\n";
            }
        }
        return "0";//json_encode($errorMessage);
    }


    public function store()
    {
//error_log("Username : " . Request::get(AllStrings::$inputUsername) . "  Password : " .  Request::get(AllStrings::$inputPassword) . " Json : " . Request::get(AllStrings::$inputObjects) . " Type : " .  Request::get(AllStrings::$inputType));






        $array = json_decode(Request::get('jsonObject'), true);


return $this->AddEntryLog($array);





        }
    }
