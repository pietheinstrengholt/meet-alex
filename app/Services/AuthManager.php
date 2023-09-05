<?php

namespace App\Services;
use App\User;
use App\Term;
use Auth;
use Gate;
use App\Collection;

class AuthManager {

	public function getEditableCollections()
	{
		//create empty collection
		$collections = collect();

		//check if the user is logged on
		if (Auth::check()) {

			//collect all collections based on the group where the user belongs to
			if (Auth::user()->group) {
				$collectionsGroup = Auth::user()->group->collections;
			} else {
				$collectionsGroup = collect();
			}

			//collect collections that the user owns
			if (Gate::allows('admin')) {
				$collectionsOwners = Collection::get();
			} else {
				//get all collections created by the user
				$collectionsOwners = Collection::where('created_by', Auth::user()->id)->get();
			}

			$collectionsUsers = Auth::user()->collections;

			//merge all collections
			$collections = $collectionsOwners->merge($collectionsGroup);
			$collections = $collections->merge($collectionsUsers);

			//make the collection unique
			$collections->unique();
		}

		//return collection of private collections based on collection_name
		return $collections->sortBy('collection_name', SORT_NATURAL|SORT_FLAG_CASE);
	}

	//closed collections is the difference between all open collections and editable
	public function getClosedCollections()
	{
		//get all public collections
		$publicCollections = Collection::where('public', 1)->get();

		//remove the collections that are private
		$diff = $publicCollections->diff($this->getEditableCollections());

		return $diff->sortBy('collection_name');
	}

	public function getReadableCollections()
	{
		//readable collections is a combination of private and non-private
		$editableCollections = $this->getEditableCollections();
		$closedCollections = $this->getClosedCollections();

		//get all collections where the user has permissions on
		$collections = $closedCollections->merge($editableCollections);
		$collections = $collections->sortBy('collection_name', SORT_NATURAL|SORT_FLAG_CASE);

		return $collections;
	}
}

?>
