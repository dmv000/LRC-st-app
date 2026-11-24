<?php

namespace App\Http\ORM;

use Eloquent;
use App\Http\ORM\station as Station;

class location extends Eloquent
{
    protected $table = "location";
    public $timestamps = false;

    public function station()
    {

        return $this->belongsTo(Station::class, 'station');
    }
}
