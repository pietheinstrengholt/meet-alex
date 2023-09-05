<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Collection;
use App\Term;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Session;
use Validator;

class SearchController extends Controller
{
	public function search(Request $request)
	{
		//validate input form
		$this->validate($request, [
			'search' => 'required'
		]);

		if ($request->isMethod('post') || $request->isMethod('get')) {
			if ($request->has('advanced-search')) {
				if ($request->input('advanced-search') == "yes") {
					//validate input form
					$this->validate($request, [
						'collections' => 'required',
					]);
				}
			}

			//search using the crawlAll function
			$limit = 250;
			$results = $this->crawlAll(trim($request->input('search')), $limit);

			//get editableCollections
			$editableCollections = app('auth.manager')->getEditableCollections();

			return view('search.index', compact('results','editableCollections'));
		}
	}

	public function searchApi(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'search' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		if (is_numeric($request->input('limit'))) {
			$limit = $request->input('limit', 10);
		}

		$results = $this->crawlAll(trim($request->input('search')), $limit);

		return response()->json($results);
	}

	public function crawlAll($input, $limit) {
		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collectionList = explode(',', app('auth.manager')->getReadableCollections()->implode('id', ','));

		//perform first search starting on the first part of the collection name
		$collections = Collection::whereIn('id', $collectionList)->where('collection_name', 'like', $input . '%')->orderBy('collection_name', 'asc')->take($limit)->get();

		//check if the first result already contains the limit
		if ($collections->count() < $limit) {
			//perform second search on the other part of the colleciton name
			$merged = $collections->merge(Collection::whereIn('id', $collectionList)->where('collection_name', 'like', '%' . $input . '%')->orderBy('collection_name', 'asc')->take($limit - $collections->count())->get());
		} else {
			return $collections;
			exit();
		}

		if ($merged->count() < $limit) {
			//perform third search on the first part of the term name
			$secondmerge = $merged->merge(Term::with('collection')->whereIn('collection_id', $collectionList)->where('term_name', 'like', $input . '%')->orderBy('term_name', 'asc')->take($limit - $merged->count())->get());
		} else {
			return $merged;
			exit();
		}

		if ($secondmerge->count() < $limit) {
			//perform fourth search on the other part of the term name
			$thirdmerge = $secondmerge->merge(Term::with('collection')->whereIn('collection_id', $collectionList)->where('status_id', 1)->where('term_name', 'like', '%' . $input . '%')->orderBy('term_name', 'asc')->take($limit - $secondmerge->count())->get());
		} else {
			return $secondmerge;
			exit();
		}

		if ($thirdmerge->count() < $limit) {
			//perform fifth search on the term definition
			$fourthmerge = $thirdmerge->merge(Term::with('collection')->whereIn('collection_id', $collectionList)->where('status_id', 1)->where('term_definition', 'like', '%' . $input . '%')->orderBy('term_name', 'asc')->take($limit - $thirdmerge->count())->get());
		} else {
			return $thirdmerge;
			exit();
		}

		//TODO: return based on the order of building up of the structure
		return $fourthmerge;
	}

}
