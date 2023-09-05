<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
	use Notifiable;
	use SoftDeletes;

	/**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = ['name', 'nickname', 'email', 'role', 'password', 'group_id', 'provider', 'provider_id'];
	protected $dates = ['deleted_at'];

	public function group()
	{
		return $this->hasOne('App\Group', 'id', 'group_id');
	}

	public function getGravatarAttribute()
	{
		$hash = md5(strtolower(trim($this->attributes['email'])));
		return "http://www.gravatar.com/avatar/$hash";
	}

	public function collections()
	{
		return $this->belongsToMany('App\Collection', 'user_collection', 'user_id', 'collection_id');
	}

	public function bookmarks()
	{
		return $this->belongsToMany('App\Collection', 'user_collection_bookmarks', 'user_id', 'collection_id');
	}

	public function getFullDetailsAttribute()
	{
		if (!empty($this->nickname)) {
			return $this->nickname;
		} else {
			return $this->name;
		}
	}

	public function getDisplayNameAttribute()
	{
		if (!empty($this->nickname)) {
			return $this->nickname;
		} else {
			return $this->name;
		}
	}

	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = [
		'password', 'remember_token',
	];
}
