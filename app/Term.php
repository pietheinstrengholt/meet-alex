<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Term extends Model
{
	protected $fillable = ['id', 'collection_id', 'term_name', 'term_definition', 'current', 'version', 'archived', 'status_id', 'owner_id', 'created_by'];
	protected $guarded = [];
	public $incrementing = false;
	protected $appends = ['term_definition_stripped'];

	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope('archived', function(Builder $builder) {
			$builder->where('archived', 0)->where('current', 1);
		});
	}

	public function collection()
	{
		return $this->belongsTo('App\Collection');
	}

	public function objects()
	{
		return $this->hasMany('App\Ontology','subject_id','id')->has('object')->with('object','relation');
	}

	public function subjects()
	{
		return $this->hasMany('App\Ontology','object_id','id')->has('subject')->with('subject','relation');
	}

	public function collections()
	{
		return $this->belongsToMany('App\Collection', 'collection_term', 'term_id', 'collection_id');
	}

	public function comments()
	{
		return $this->hasMany('App\Comment');
	}

	public function status()
	{
		return $this->belongsTo('App\Status','status_id','id');
	}

	public function owner()
	{
		return $this->belongsTo('App\User','owner_id','id')->withTrashed();
	}

	public function getHomonymsAttribute()
	{
		$homonyms = Term::where('term_name', $this->attributes['term_name'])->where('id', '<>', $this->attributes['id'])->with('collection')->get();
		$homonymsCollection = collect();

		foreach ($homonyms as $homonym) {
			//filter out any of the terms having the same collection_id
			$homonymsCollection->push($homonym);
		}

		$homonymsCollection = $homonymsCollection->unique();

		return $homonymsCollection;
	}

	public function getSynonymsAttribute()
	{
		$synonymsCollection = collect();

		//parse all objects
		if (!empty($this->objects)) {
			foreach ($this->objects as $object) {
				if ($object->relation->relation_name == "is same as" || $object->relation->relation_name == "is synonym of") {
					$synonymsCollection->push($object->object);
				}
			}
		}

		//parse all subjects
		if (!empty($this->subjects)) {
			foreach ($this->subjects as $subject) {
				if ($subject->relation->relation_name == "is same as" || $subject->relation->relation_name == "is synonym of") {
					$synonymsCollection->push($subject->subject);
				}
			}
		}

		$synonymsCollection = $synonymsCollection->unique();

		return $synonymsCollection;
	}

	//return the average star amount
	public function getStarAverageAttribute()
	{
		return round($this->stars->avg('rating'));
	}

	public function getTermDefinitionStrippedAttribute()
	{
		return trim(strip_tags($this->attributes['term_definition']));
	}

	public function getHasUnfetchedRelationsAttribute()
	{
		$ontologies = Ontology::where('subject_id', $this->attributes['id'])->orWhere('object_id', $this->attributes['id'])->get();
		if ($ontologies->count()) {
			return true;
		} else {
			return false;
		}
	}

	public function getStatusNameAttribute()
	{
		if ($this->status) {
			return $this->status->status_name;
		}
	}

	public function getHistoryAttribute()
	{
		$terms = Term::withoutGlobalScopes()->where('id', $this->attributes['id'])->where('collection_id', $this->attributes['collection_id'])->where('current', '<>', 1)->get();
		if ($terms->count()) {
			return true;
		} else {
			return false;
		}
	}

	public function stars()
	{
		return $this->hasMany('App\TermStar');
	}
}

?>
