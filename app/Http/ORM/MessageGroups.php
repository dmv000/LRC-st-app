<?php

namespace App\Http\ORM;

use Eloquent;

class MessageGroups extends Eloquent
{
    protected $table = "message_groups";

    protected $fillable = [
        'message_id',
        'group_id'
    ];

    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}
