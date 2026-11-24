<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'objectsid', 'id', 'name', 'nickname', 'password',
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->hasMany('App\Http\ORM\GroupMember','user_id','id');
    }

    public function unread_news()
    {
        return $this->hasMany('App\Http\ORM\News','user_id','id')->where('is_read',0)->orderBy('created_at','desc');
    }

    public function surveys()
    {
        return $this->hasMany('App\Http\ORM\Survey','user_id','id')->where('is_filled',0)->where('is_deleted',0)->count();
    }

    public function news()
    {
        return $this->hasMany('App\Http\ORM\News','user_id','id')->limit(5)->orderBy('created_at','desc');
    }

    public function memberOf($groupName) {
	$group = \App\Http\ORM\Group::where('description',$groupName)->first();
	if($group == null) return false;

	try {

		$g = $group;
		$member = $this->groups()->get()->contains('group_id',$g->id);

		if($member == true) {
	
			return true;
		} else {

			if($g->parent_group == null) return false;
			return $this->memberOf( \App\Http\ORM\Group::where('id',$g->parent_group)->first()->description );
		}


	}catch(\Exception $e){

		return false;
	}
    }


}
