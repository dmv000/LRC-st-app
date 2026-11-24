<?php

namespace App\Http\ORM;

use Eloquent;
class EventType extends Eloquent
{
    protected $table = "event_type";
    public $timestamps = false;
}
