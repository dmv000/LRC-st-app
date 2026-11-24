<?php

namespace App\Http\ORM;

use Eloquent;
use App\User as User;

class Survey extends Eloquent
{
    protected $table = "survey";

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
