<?php

namespace App\Http\ORM;

use Eloquent;
class EmergencyCallSource extends Eloquent
{
    protected $table = "emergency_calls_source";
	protected $guarded = ['id' , 'updated_at' , 'created_at'];
}
