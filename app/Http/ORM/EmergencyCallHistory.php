<?php

namespace App\Http\ORM;

use Eloquent;
class EmergencyCallHistory extends Eloquent
{
    protected $table = "emergency_calls_history";
	protected $guarded = ['id' , 'updated_at' , 'created_at'];
}
