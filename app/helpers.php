<?php

use Illuminate\Support\Facades\Log;
use App\Http\ORM\Access;

function unauthorized($location,$user) {

	Log::warning('User ' . $user->nickname . ' tried accessing ' . $location );
}

function hasAccessLevel($id,$access) {

	$acc = Access::where('user_id',$id)->where('name',$access)->get();
	if(is_null($acc) || count($acc) == 0)
		return false;
	else
		return true;
}

