<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	protected $fillable = ['group_name','group_description'];
	protected $guarded = [];

	public function users()
	{
		return $this->hasMany('App\User');
	}

	public function collections()
	{
		return $this->belongsToMany('App\Collection', 'collection_group', 'collection_id', 'group_id');
	}
}

?>
