<?php

namespace App\Http\ORM;

use Eloquent;
use App\Http\ORM\PointAction;

class UserPoint extends Eloquent
{
    protected $table = "user_point";

    public function pointAction()
    {

        return $this->belongsTo(PointAction::class);
    }
}