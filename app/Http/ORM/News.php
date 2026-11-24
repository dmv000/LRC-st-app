<?php

namespace App\Http\ORM;

use Eloquent;
class News extends Eloquent
{
    protected $table = "news";

    public function time() {

	$diff = strtotime("now") - strtotime($this->created_at);
	if($diff < 60 ) {

		return "Less than one minute ago";
	}

	if($diff < 3600 ) {

		return round($diff / 60). " minutes ago";
	}

	if($diff < 3600 * 24) {

		return round($diff / (60 * 60 ) ) . " hours ago";
	}
	
	return date("M j Y");

    }

    public function message() {

	return mb_strimwidth($this->description, 0, 100, '...');
    }
}
