<?php

namespace App\Http\ORM;

use App\User;
use Eloquent;

class Message extends Eloquent
{
    protected $table = "message";

    protected $fillable = [
        'subject',
        'content',
        'response_type',
        'response_options',
        'user_id',
        'status'
    ];

    public function groups()
    {
        return $this->hasMany(MessageGroups::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(UserMessage::class);
    }
}
