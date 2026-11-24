<?php

namespace App\Http\ORM;

use Eloquent;
class MissionDriver extends Eloquent
{
    protected $table = "mission_driver";
    public $primaryKey= "driver_id";
}

