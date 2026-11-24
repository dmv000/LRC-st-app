<?php

namespace App\Http\ORM;

use Eloquent;
class EmergencyCallPatient extends Eloquent
{
    protected $table = "emergency_calls_patient";
	protected $guarded = ['id' , 'updated_at' , 'created_at'];
}
