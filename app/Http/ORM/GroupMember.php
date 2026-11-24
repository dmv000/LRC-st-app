<?php

namespace App\Http\ORM;

use App\User;
use Eloquent;

class GroupMember extends Eloquent
{
    protected $table = "group_member";

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
