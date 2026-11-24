<?php

namespace App\Http\ORM;

use Eloquent;
use Auth;

class Event extends Eloquent
{
    protected $table = "event_log";

    public function create($eventType , $description) {

	$et = EventType::where('description' , $eventType)->first();
	if($et == null) {

		$et = new EventType;
		$et->description = $eventType;
		$et->save();
	}
	$this->event_type = EventType::where('description' , $eventType)->first()->id;
	$this->user_id = Auth::user()->id;
	$this->details = $description;
	$this->save();
    }

}
