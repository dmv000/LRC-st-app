<?php

namespace App\Http\ORM;

use Eloquent;
use App\Http\ORM\UserPoint;

class PointAction extends Eloquent
{
    protected $table = "point_action";

    public $timestamps = false;

    public function userPoints()
    {
        return $this->hasMany(UserPoint::class);
    }
}
