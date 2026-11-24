<?php

namespace App\Http\ORM;

use Eloquent;
class MissionLeader extends Eloquent
{
    protected $table = "mission_mission_leader";
    public $primaryKey = 'mission_leader_id';
}
