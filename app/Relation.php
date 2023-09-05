<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
	protected $fillable = ['collection_id','relation_name','relation_description','created_by'];
	protected $guarded = [];
	protected $table = 'relation_types';

	public function collection()
	{
		return $this->belongsTo('App\Collection');
	}

	public function getRelationNameAttribute($value)
	{
		return strtolower($value);
	}
}

?>
