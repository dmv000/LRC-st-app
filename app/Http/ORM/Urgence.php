<?php

namespace App\Http\ORM;

use Eloquent;
class Urgence extends Eloquent
{
    protected $table = "urgence";

    public function emts()
    {
        return $this->hasMany('App\Http\ORM\MissionEMT','mission_id','id');
    }

    public function material()
    {
        return $this->hasMany('App\Http\ORM\MissionMaterial','mission_id','id');
    }

    public function documents()
    {
        return $this->hasMany('App\Http\ORM\MissionDocument','mission_id','id');
    }


    public function driver()
    {
        return $this->hasOne('App\Http\ORM\MissionDriver','mission_id');
    }

    public function leader()
    {
        return $this->hasOne('App\Http\ORM\MissionLeader','mission_id');
    }

    public function type() {

	return App\Http\ORM\MissionType::where('id', '=', $this->type)->get();
   }


    public function users(){
        $vals = array();
        $vals[] = $this->hasMany('App\Http\ORM\MissionEMT','mission_id','id');
        $vals[] = $this->hasOne('App\Http\ORM\MissionDriver','mission_id');
        $vals[] = $this->hasOne('App\Http\ORM\MissionLeader','mission_id');
        return $this->hasOne('App\Http\ORM\MissionLeader','mission_id');
    }
}
