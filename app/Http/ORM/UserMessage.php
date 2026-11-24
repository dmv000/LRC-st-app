<?php

namespace App\Http\ORM;

use App\User;
use Eloquent;

class UserMessage extends Eloquent
{
    protected $table = "user_message";

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'message_id',
        'status',
        'reason',
        'identifier',
        'response',
        'ack'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
