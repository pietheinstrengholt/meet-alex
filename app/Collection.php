<?php

namespace App;
use Auth;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
	protected $fillable = ['parent_id','collection_name','collection_description','public','receive_notifications','created_by'];
	protected $guarded = [];
	protected $table = 'collections';
	protected $appends = ['term_count','ontologies_count','owner_name','bookmarked'];

	public function terms()
	{
		return $this->hasMany('App\Term')->orderBy('term_name', 'asc');
	}

	public function links()
	{
		return $this->belongsToMany('App\Term', 'collection_term', 'collection_id', 'term_id')->orderBy('term_name', 'asc');
	}

	public function getCombinationsAttribute()
	{
		// There two calls return collections
		// as defined in relations.
		$combinationTerms = $this->terms;
		$combinationLinks = $this->links;

		// Merge collections and return single collection.
		return $combinationTerms->merge($combinationLinks)->sortBy('term_name');
	}

	// limit the length of collection name if needed
	public function getShortNameAttribute()
	{
		if (strlen($this->attributes['collection_name']) > 20) {
			return substr($this->attributes['collection_name'],0,20) . '...';
		} else {
			return $this->attributes['collection_name'];
		}
	}

	// limit the length of collection name if needed
	public function getShortDescriptionAttribute()
	{
		if (strlen($this->attributes['collection_description']) > 100) {
			return substr($this->attributes['collection_description'],0,100) . '...';
		} else {
			return $this->attributes['collection_description'];
		}
	}

	public function relations()
	{
		return $this->hasMany('App\Relation')->orderBy('relation_name', 'asc');
	}

	public function owner()
	{
		return $this->belongsTo('App\User','created_by','id')->withTrashed();
	}

	public function groups()
	{
		return $this->belongsToMany('App\Group', 'collection_group', 'collection_id', 'group_id');
	}

	public function getTermCountAttribute()
	{
		return Term::where('collection_id', $this->attributes['id'])->count();
	}

	public function getOntologiesCountAttribute()
	{
		return Ontology::where('collection_id', $this->attributes['id'])->count();
	}

	public function getOwnerNameAttribute()
	{
		$owner = User::where('id', $this->attributes['created_by'])->select('name')->first();
		if ($owner) {
			return $owner->name;
		}
	}

	public function getBookmarkedAttribute()
	{
		if (Auth::check()) {
			return Auth::user()->bookmarks->contains($this->attributes['id']);
		} else {
			return false;
		}
	}

	public function parent()
	{
		return $this->belongsTo('App\Collection', 'parent_id');
	}

	public function children()
	{
		return $this->hasMany('App\Collection', 'parent_id');
	}

	public function users()
	{
		return $this->belongsToMany('App\User', 'user_collection', 'collection_id', 'user_id');
	}

	public function followers()
	{
		return $this->belongsToMany('App\User', 'user_collection_bookmarks', 'collection_id', 'user_id');
	}

	public function ontologies()
	{
		return $this->hasMany('App\Ontology', 'collection_id');
	}

	public function contributors() {
		//create empty collection
		$owners = collect();

		//add all users based on group rights
		foreach($this->groups as $group) {
			foreach($group->users as $user) {
				$owners->push($user);
			}
		}

		//add all users based on role admin
		$admins = User::where('role', 'admin')->get();
		$owners->merge($admins);

		//add the owner (creator) of the collection
		if (!empty($this->owner)) {
			$owners->push($this->owner);
		}

		//add all the users that have rights
		if (!empty($this->users)) {
			foreach($this->users as $user) {
				$owners->push($user);
			}
		}

		//nake owners unique
		$owners->unique();

		//sort owners basedd on name
		$owners = $owners->sortBy('name');

		return $owners;
	}
}

?>
