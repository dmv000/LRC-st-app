<?php

namespace App\Http\ORM;

use Eloquent;
class MissionEMT extends Eloquent
{
    protected $table = "mission_emts";
    public $primaryKey = 'emt_id';
}
