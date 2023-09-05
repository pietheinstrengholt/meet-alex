<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Term;
use App\Collection;
use App\Ontology;
use App\Status;
use App\Relation;
use App\User;
use App\Comment;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use App\Helpers\TermHelper;
use App\VersionComment;
use Event;
use App\Events\TermChanged;
use DB;
use App\Http\Requests\CreateTermRequest;

class TermController extends Controller
{
	public function index(Request $request)
	{
		abort(403, 'Directly accessing terms does not work.');
	}

	public function show(Collection $collection, Term $term, Request $request)
	{
		//check if id property exists
		if (!$term->id) {
			abort(403, 'This term no longer exists in the database.');
		}

		//get all comments for this term
		$comments = Comment::where('term_id', $term->id)->orderBy('created_by', 'desc')->get();

		//lazy load term objects, subjects
		$term->load('objects','subjects');

		//get editable collections, see App\AuthService
		$editableCollections = app('auth.manager')->getEditableCollections();

		//check for fullscreen argument, set to variable
		if ($request->has('fullscreen')) {
			$fullscreen = true;
		} else {
			$fullscreen = false;
		}

		//if false, it will show the term in the visual, if true it will show the complete collection/model
		$modelView=false;

		return view('terms.show', compact('collection','term','comments','editableCollections', 'fullscreen', 'modelView'));
	}

	public function edit(Collection $collection, Term $term, Request $request)
	{
		//check if id property exists
		if (!$term->id) {
			abort(404, 'This term no longer exists in the database.');
		}

		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, 'Unauthorized action.');
		}

		//only show users that are allowed to contribute to the collection
		$owners = $collection->contributors();

		//get all statuses, except the first one
		$statuses = Status::orderBy('id', 'asc')->get();

		if (count($statuses) == 0) {
			abort(400, '400 Bad Request. No statuses found in the database. Please ask the administrator to create a status.');
		}

		//lazy load term objects
		$term->load('objects');

		$collection->load('relations');

		return view('terms.edit', compact('collection','term','statuses','owners'));
	}

	public function relink(Collection $collection, Term $term, Request $request) {

		//check if collection exists
		if (!$collection->id) {
			abort(404, 'This collection no longer exists in the database.');
		}

		//check if term exists
		if (!$term->id) {
			abort(404, 'This term no longer exists in the database.');
		}

		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, 'Unauthorized action.');
		}

		//find the changed terms in many to many table
		$changedTerm = DB::table('collection_term')->where('term_id', $term->id)->where('collection_id', $collection->id)->first();

		//check if changedTerm exists
		if (empty($changedTerm->id)) {
			abort(404, 'Unable to find the term that has been changed.');
		}

		//get old term version from the database
		$oldTerm = Term::withoutGlobalScopes()->where('id', $term->id)->where('version', $changedTerm->version)->first();

		return view('terms.relink', compact('collection','term','oldTerm'));
	}

	public function create(Collection $collection, Term $term, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//only show users that are allowed to contribute to the collection
		$owners = $collection->contributors();

		$statuses = Status::orderBy('status_name', 'asc')->get();
		$collection->load('relations');
		$groupedObjects = collect();

		return view('terms.create', compact('collection','term','statuses','owners','groupedObjects'));
	}

	public function bulkcreate(Collection $collection, Term $term, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//only show users that are allowed to contribute to the collection
		$owners = $collection->contributors();

		$statuses = Status::orderBy('status_name', 'asc')->get();
		$collection->load('relations');
		$groupedObjects = collect();

		return view('terms.bulkcreate', compact('collection','term','statuses','owners','groupedObjects'));
	}

	public function store(Collection $collection, CreateTermRequest $request)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//do a custom validation on the term name
		$terms = Term::where('collection_id', $request->input('collection_id'))->where('term_name', $request->input('term_name'))->get();

		//if terms with the same name are found, redirect back with input and error message
		if ($terms->count()) {
			return Redirect::back()->withInput()->withErrors(['A term with the same name for this Collection already exists.']);
		}

		try {
			app('term.manager')->create($request);
		} catch(\Exception $exception) {
			return Redirect::back()->withInput()->withErrors([$exception->getMessage()]);
		}

		//check if linked term with same name not already exists
		$links = $collection->links->where('term_name', $request->input('term_name'));
		if ($links->count()) {
			return Redirect::back()->withInput()->withErrors(['A linked term with the same name for this Collection already exists.']);
		}

		if (!ctype_alpha(substr($request->input('term_name'), 0, 1))) {
			return Redirect::to('/collections/' . $collection->id . '/?letter=[0-9]')->with('message', 'Term created.');
		} else {
			return Redirect::to('/collections/' . $collection->id . '/?letter=' . strtoupper(substr($request->input('term_name'), 0, 1)))->with('message', 'Term created.');
		}
	}

	public function update(Collection $collection, Term $term, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'id' => 'required',
			'term_name' => 'required',
			'collection_id' => 'required',
			'status_id' => 'required',
			'owner_id' => 'required'
		]);

		//if the archive button is pressed, archive term and its relations
		if ($request->has('archive')) {
			Term::where('id', $term->id)->update(['archived' => 1]);
			Ontology::where('subject_id', $term->id)->update(['archived' => 1]);
			Ontology::where('object_id', $term->id)->update(['archived' => 1]);
			return Redirect::to('/collections/' . $collection->id)->with('message', 'Term archived.');
		}

		if ($request->has('edit')) {
			Term::where('id', $term->id)->where('version', $term->version)->update(['current' => 0]);

			$term = Term::create([
				'id' => $term->id,
				'collection_id' => $request->input('collection_id'),
				'term_name' => $request->input('term_name'),
				'term_definition' => $request->input('term_definition'),
				'version' => $term->version + 1, 'status_id',
				'status_id' => $request->input('status_id'),
				'owner_id' => $request->input('owner_id'),
				'created_by' => Auth::user()->id
			]);

			//delete existing ontologies
			Ontology::where('collection_id', $collection->id)->where('subject_id', $term->id)->delete();

			//re-create new relations as received from form
			if ($request->has('Relations')) {

				//process relations
				foreach($request->input('Relations') as $ontology) {

					//check if at least an object and relation name is present
					if (!empty($ontology['object_id']) && !empty($ontology['relation_name'])) {

						//retrieve the relation by the attributes provided, or create it if it doesn't exist...
						$relation = Relation::where('collection_id', $collection->id)->where('relation_name', trim($ontology['relation_name']))->orderBy('relation_name', 'asc')->first();
						if (empty($relation)) {
							$relation = Relation::create(['collection_id' => $collection->id, 'relation_name' => trim($ontology['relation_name']), 'created_by' => Auth::user()->id]);
						}

						//check if not the same relations are passed
						$checkForDupplicate = Ontology::where('collection_id', $collection->id)->where('subject_id', $term->id)->where('relation_id', $relation->id)->where('object_id', $ontology['object_id'])->first();

						//add additional validation check to avoid any contraints on the database. The object passed must be valid.
						$checkForObject = Term::where('id', $ontology['object_id'])->first();

						//check if the check is empty
						if (empty($checkForDupplicate) && !empty($checkForObject)) {

							//create new relation
							Ontology::create(['collection_id' => $collection->id, 'subject_id' => $term->id, 'relation_id' => $relation->id, 'object_id' => $ontology['object_id'], 'status_id' => $request->input('status_id'), 'created_by' => Auth::user()->id]);
						}
					}
				}
			}
		}

		//fire an event that the term has been changed
		Event::dispatch(new TermChanged($term, Auth::user()));

		//redirect based on the first character
		if (!ctype_alpha(substr($request->input('term_name'), 0, 1))) {
			return Redirect::to('/collections/' . $collection->id . '/?letter=[0-9]')->with('message', 'Term updated.');
		} else {
			return Redirect::to('/collections/' . $collection->id . '/?letter=' . strtoupper(substr($term->term_name, 0, 1)))->with('message', 'Term updated.');
		}
	}

	public function updateLink(Collection $collection, Term $term, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		if ($request->has('clone')) {

			//retrieve term with older version
			$termOld = Term::withoutGlobalScopes()->where('id', $term->id)->where('version', $request->input('version'))->first();

			//check if result is not empty
			if (!empty($termOld)) {

				//do a custom validation on the term name
				$terms = Term::where('collection_id', $collection->id)->where('term_name', $termOld->term_name)->get();

				//if terms with the same name are found, redirect back with input and error message
				if ($terms->count()) {
					return Redirect::back()->withInput()->withErrors(['A term with the same name for this Collection already exists.']);
				}

				//remove link and clone term if approval has given
				$collection->links()->detach($term);

				//create new term from old term version
				Term::create([
					'id' => TermHelper::returnMaxId(),
					'collection_id' => $collection->id,
					'term_name' => $termOld->term_name,
					'term_definition' => $termOld->term_definition,
					'version' => 1,
					'status_id' => 1,
					'owner_id' => Auth::user()->id,
					'created_by' => Auth::user()->id
				]);
			}

			return Redirect::to('/collections/' . $term->collection_id)->with('message', 'Term copied and link removed.');
		}

		if ($request->has('keep')) {
			return Redirect::to('/collections/' . $term->collection_id)->with('message', 'Term link updated.');
		}
	}

	public function destroy(Collection $collection, Term $term)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check if id property exists
		if (!$term->id) {
			abort(404, '404 Not Found. This term no longer exists in the database.');
		}

		//delete all related content before deleting term
		Ontology::where('subject_id', $term->id)->delete();
		Ontology::where('object_id', $term->id)->delete();
		Comment::where('term_id', $term->id)->delete();
		$term->delete();

		return Redirect::to('/collections/' . $collection->id . '/?letter=' . strtoupper(substr($term->term_name, 0, 1)))->with('message', 'Term and related ontology deleted.');
	}

	public function trail(Collection $collection, Term $term)
	{
		//check for the right privileges
		if (Gate::denies('contribute-to-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		$terms = Term::withoutGlobalScopes()->where('id', $term->id)->where('collection_id', $term->collection_id)->orderBy('version', 'desc')->get();

		return view('terms.trail', compact('term','terms','collection'));
	}

	public function imageUpload(Request $request)
	{
		//create upload folder, if not exists
		if (!file_exists(public_path() . '/img/upload/')) {
			mkdir(public_path() . '/img/upload/', 0777, true);
		}
		//upload image with random string
		$file = $request->file('imagefile');
		$extension = $file->getClientOriginalExtension();
		$validExtensions = array("jpeg", "jpg", "png", "gif");
		if (in_array(strtolower($extension), $validExtensions)) {
			$random = str_random(10);
			$file->move(public_path() . '/img/upload/', $random . '.' . $extension);
			$file_path = str_replace("/index.php","",url('')) . '/img/upload/' . $random . '.' . $extension;
			return view('imageupload.image-upload', compact('file_path'));
		} else {
			$error = "An error occurred while processing the image. Unknown extension type.";
			return view('imageupload.image-upload', compact('error'));
		}
	}
}
