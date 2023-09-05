<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Collection;
use App\Term;
use App\TermStar;
use App\User;
use App\Status;
use App\DefaultRelation;
use App\Relation;
use App\Comment;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use App\Group;
use DB;
use Session;
use App\Helpers\Format;
use Event;
use App\Events\UserBookmarkedCollection;
use App\Events\OwnerApprovedCollectionRights;
use Mail;
use App\Mail\NotifyAboutInterestingContent;
use App\Mail\NotifyOwnerAboutCollaborationRequest;

class CollectionController extends Controller
{
	public function index()
	{
		//get ediablecollections and publiccollections
		$editableCollections = app('auth.manager')->getEditableCollections();
		$closedCollections = app('auth.manager')->getClosedCollections();

		if (Auth::check()) {
			//get bookmarks
			$bookmarks = Auth::user()->bookmarks;

			//if bookmarks is not empty add any bookmark to the editablecollections container
			$editableCollections = $editableCollections->merge($bookmarks);

			//filter out any bookmarks from publiccollections container
			$closedCollections = $closedCollections->filter(function ($value, $key) use ($bookmarks) {
				if (!$bookmarks->contains($value)) {
					return $value;
				}
			});
		}

		//sort ediablecollections and publiccollections
		$editableCollections = $editableCollections->sortBy('collection_name', SORT_NATURAL|SORT_FLAG_CASE);
		$closedCollections = $closedCollections->sortBy('collection_name', SORT_NATURAL|SORT_FLAG_CASE);

		return view('collections.index', compact('editableCollections','closedCollections'));
	}

	public function edit(Collection $collection)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//collections for parenting, only get collections where the user has rights on
		$collections = app('auth.manager')->getEditableCollections();

		//get all group names
		$groups = Group::orderBy('group_name', 'asc')->get();

		//contributors in this case is a combination of followers and users which have directly assigned access to modify
		$followers = $collection->followers;
		$contributors = $collection->users;
		$contributors = $contributors->merge($followers);
		$contributors->unique();
		$contributors->sortBy('name');

		return view('collections.edit', compact('collection','groups','collections','contributors'));
	}

	public function create(Collection $collection)
	{
		//check for the right privileges
		if (!Auth::check()) {
			return redirect('/login');
		}

		//get all group names
		$groups = Group::orderBy('group_name', 'asc')->get();

		//collections for parenting, only get collections where the user has rights on
		$collections = app('auth.manager')->getEditableCollections();

		return view('collections.create', compact('collection','statuses','groups','collections'));
	}

	public function returnLetter($letters, $request, $collection) {

		//set default to null
		$letter = null;

		//if letters are not empty check if letter is set with argument, else take first letter from array
		if (!empty($letters)) {

			//set term count based on collection
			if ($collection) {
				$termCount = $collection->term_count;
			} else {
				$termCount = 0;
			}

			//if the termcount is more than 1000 or all collections are used (collection is empty)
			if ($request->has('letter') || empty($collection) || $termCount > 1000) {

				//set the letter variable to the input, will be used as variable in the blade
				if ($request->has('letter')) {
					$letter = $request->input('letter');
					//abort if the letter provided cannot be found in the letters array
					if (!in_array($request->input('letter'), $letters)) {
						abort(404, '404 Not Found. Letter not found in collection.');
					}
				} else {
					$letter = $letters[0];
				}
			}
		}

		return $letter;
	}

	//return only the terms back based on the letter provided
	public function filterTerms($letter, $terms) {

		//check if letter is alphabetic character
		if (ctype_alpha($letter)) {
			//filter and return only corresponding terms matching the letter
			$terms = $terms->filter(function ($value, $key) use ($letter) {
				return strtoupper(substr($value->term_name, 0, 1)) == $letter;
			});
		}

		//check if letter is non-alphabetic character
		if ($letter == "[0-9]") {
			//set manually non-alphabetic character
			$terms = $terms->filter(function ($value, $key) {
				return !ctype_alpha(substr($value->term_name, 0, 1));
			});
		}

		//natural sort terms on term_name
		$terms = $terms->sortBy('term_name', SORT_NATURAL|SORT_FLAG_CASE);

		return $terms;
	}

	//return personal stars
	public function returnStarsArray($terms) {
		//collect all personal starred terms
		if (Auth::check()) {
			$stars = TermStar::where('created_by', Auth::user()->id)->whereIn('term_id', explode(',', $terms->implode('id', ',')))->select('term_id', 'rating')->get();
		} else {
			$stars = collect();
		}
		$stars = $stars->pluck('rating', 'term_id')->toArray();

		return $stars;
	}

	public function allTerms(Request $request) {
		//get list with editable collections
		$editableCollections = app('auth.manager')->getEditableCollections();
		$readableCollections = app('auth.manager')->getReadableCollections();
		$terms = Term::whereIn('collection_id', explode(',', $readableCollections->implode('id', ',')))->orderBy('term_name', 'asc')->get();

		$collection = null;

		//use returnLetters to get an array with all first letters from all terms
		$letters = Format::returnLetters($terms);
		//get either the first letter or the letter from the request
		$letter = $this->returnLetter($letters, $request, $collection);
		//filter the terms based on the letter
		$terms = $this->filterTerms($letter, $terms);
		//get stars based on the terms
		$stars = $this->returnStarsArray($terms);

		// if visualShow paremeter is given, the visual will be started with the complete model
		if ($request->has('visualShow')) {
			$fullscreen = true;
			$modelView = true; // if false, it will show the term in the visual, if true it will show the complete collection/model
		}

		//show distinct blade page
		return view('collections.show', compact('collection','letter','letters','editableCollections','readableCollections','terms', 'fullscreen', 'modelView', 'stars'));
	}

	public function show(Collection $collection, Request $request)
	{
		//get list with editable collections
		$editableCollections = app('auth.manager')->getEditableCollections();
		$readableCollections = app('auth.manager')->getReadableCollections();
		$terms = $collection->combinations;

		//use returnLetters to get an array with all first letters from all terms
		$letters = Format::returnLetters($terms);
		//get either the first letter or the letter from the request
		$letter = $this->returnLetter($letters, $request, $collection);
		//filter the terms based on the letter
		$terms = $this->filterTerms($letter, $terms);
		//get stars based on the terms
		$stars = $this->returnStarsArray($terms);

		// if visualShow paremeter is given, the visual will be started with the complete model
		if ($request->has('visualShow')) {
			$fullscreen = true;
			$modelView = true; // if false, it will show the term in the visual, if true it will show the complete collection/model
		}

		//remove current collection from editablecollections since there is not need to clone it to current collection
		$editableCollections = $editableCollections->filter(function ($value, $key) use ($collection) {
			return $value->id != $collection->id;
		});

		// if visualShow paremeter is given, the visual will be started with the complete model
		if ($request->has('visualShow')) {
			$fullscreen = true;
			$modelView = true; // if false, it will show the term in the visual, if true it will show the complete collection/model
		}

		//show distinct blade page
		return view('collections.show', compact('collection','letter','letters','editableCollections','readableCollections','terms', 'fullscreen', 'modelView', 'stars'));
	}

	public function share(Collection $collection, Request $request)
	{
		//check if id property exists
		if (!$collection->id) {
			abort(404, 'This collection no longer exists in the database.');
		}

		//show distinct blade page
		return view('collections.share', compact('collection'));
	}

	public function collaborate(Collection $collection, Request $request)
	{
		//check if id property exists
		if (!$collection->id) {
			abort(404, 'This collection no longer exists in the database.');
		}

		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {

			//check if user is singed on
			if (Auth::check()) {
				//create user object to be passed to NotifyOwnerAboutCollaborationRequest class
				$user = Auth::user();

				//send email to collection owner's address, provide existing user and collection as arguments
				Mail::to($collection->owner->email)->send(new NotifyOwnerAboutCollaborationRequest($user, $collection->owner, $collection));
			}
		}

		//show distinct blade page
		return Redirect::route('collections.show', $collection->id)->with('message', 'The owner of the collection has been asked to grant you permissions.');
	}

	public function postshare(Collection $collection, Request $request)
	{
		//validate input form
		$this->validate($request, [
			'email' => 'required|email|max:255'
		]);

		//create user object to be passed to NotifyAboutInterestingContent class
		$user = Auth::user();

		//send email to provided address, provide existing user and collection as arguments
		Mail::to($request->input('email'))->send(new NotifyAboutInterestingContent($user, $collection));

		//show distinct blade page
		return Redirect::route('collections.show', $collection->id)->with('message', 'User has received a notification to join and collaborate on this collection.');
	}

	public function grant(Collection $collection, User $user, Request $request)
	{
		//check if id property exists
		if (!$collection->id) {
			abort(404, 'This collection no longer exists in the database.');
		}

		//check for the right privileges
		if (Auth::user()->id <> $collection->owner->id) {
			abort(404, 'You are not allowed to grant permissions to other users.');
		}

		//add new collection to personal view of user
		$user->collections()->attach($collection->id);

		//give user rights
		$collection->users()->attach($user->id);

		//send notification to the user
		Event::fire(new OwnerApprovedCollectionRights($collection, $user));

		//show distinct blade page
		return Redirect::route('collections.show', $collection->id)->with('message', 'User has has been granted for this collection.');
	}

	public function store(Request $request)
	{
		//check for the right privileges
		if (!Auth::check()) {
			abort(403, 'Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'collection_name' => 'required|min:3|unique:collections',
			'collection_description' => 'required',
			'public' => 'required',
			'receive_notifications' => 'required'
		]);

		//create collection
		$collection = Collection::create(['parent_id' => $request->input('parent_id'), 'collection_name' => $request->input('collection_name'), 'collection_description' => $request->input('collection_description'), 'public' => $request->input('public'), 'created_by' => Auth::user()->id]);

		//sync groups
		if ($request->has('groups')) {
			$collection->groups()->sync($request->input('groups'));
		}

		//get all default relations types from the database
		$defaultRelations = DefaultRelation::orderBy('relation_name', 'asc')->get();

		//create default relations for new collection based on default relations types
		if (!empty($defaultRelations)) {
			foreach ($defaultRelations as $key => $defaultRelation) {
				Relation::create(['collection_id' => $collection->id, 'relation_name' => strtolower($defaultRelation->relation_name), 'relation_description' => $defaultRelation->relation_description, 'created_by' => Auth::user()->id]);
			}
		}

		//add new collection to personal view
		Auth::user()->collections()->attach($collection->id);

		//redirect to bulkcreate page
		return redirect()->action('TermController@bulkcreate', ['id' => $collection->id]);
	}

	public function update(Collection $collection, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'collection_name' => 'required|min:3',
			'collection_description' => 'required',
			'public' => 'required',
			'receive_notifications' => 'required'
		]);

		//sync existing groups
		if ($request->has('groups')) {
			$collection->groups()->sync($request->input('groups'));
		} else {
			$collection->groups()->detach();
		}

		//sync users
		if ($request->has('users')) {
			//fire an event for all new users
			foreach ($request->input('users') as $user) {
				if (!(in_array($user, explode(',', $collection->users->implode('id', ','))))) {
					//fire an event that the term has been changed
					$userObject = User::find($user);
					Event::fire(new OwnerApprovedCollectionRights($collection, $userObject));
				}
			}

			//sync request array
			$collection->users()->sync($request->input('users'));
		} else {
			$collection->users()->detach();
		}

		$collection->update($request->all());
		return Redirect::route('collections.show', $collection->slug)->with('message', 'Collection updated.');
	}

	public function destroy(Collection $collection)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//delete collection
		$collection->delete();

		return Redirect::route('collections.index')->with('message', 'Collection deleted.');
	}

	public function bookmark(Request $request)
	{
		//get all collections where the user has permissions on
		$collections = app('auth.manager')->getReadableCollections();
		$bookmarks = Auth::user()->bookmarks;

		$collection = Collection::where('id', $request->input('collection_id'))->first();

		if ($collections->contains($collection)) {
			//add new collection to personal view
			if (!$bookmarks->contains($collection)) {
				Auth::user()->bookmarks()->attach($collection->id);

				//fire an event if an user has bookmarked the collection
				if ($collections->receive_notifications == 1) {
					Event::fire(new UserBookmarkedCollection($collection, Auth::user()));
				}

				return response()->json('bookmarked');
			} else {
				Auth::user()->bookmarks()->detach($collection->id);
				return response()->json('unbookmarked');
			}
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}
}
