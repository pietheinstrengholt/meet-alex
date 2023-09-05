<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ontology extends Model
{
	protected $fillable = ['collection_id','subject_id','status_id','relation_id','object_id','archived','created_by'];
	protected $guarded = [];
	protected $table = 'ontologies';

	public function subject()
	{
		return $this->belongsTo('App\Term','subject_id','id');
	}

	public function object()
	{
		return $this->belongsTo('App\Term','object_id','id');
	}

	public function status()
	{
		return $this->belongsTo('App\Status','status_id','id');
	}

	public function relation()
	{
		return $this->belongsTo('App\Relation','relation_id','id');
	}
}

?>
