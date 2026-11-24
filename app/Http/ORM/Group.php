<?php

namespace App\Http\ORM;

use Eloquent;

class Group extends Eloquent
{
    protected $table = "group";

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }
}
