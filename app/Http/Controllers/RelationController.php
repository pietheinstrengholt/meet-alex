<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Relation;
use App\User;
use Auth;
use Gate;
use App\Collection;
use Illuminate\Http\Request;
use Redirect;
class RelationController extends Controller
{
	public function index(Collection $collection)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, 'Unauthorized action.');
		}

		$relations = Relation::where('collection_id',$collection->id)->orderBy('relation_name', 'asc')->get();
		return view('relations.index', compact('collection','relations'));
	}

	public function edit(Collection $collection, Relation $relation)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check if id property exists
		if (!$relation->id) {
			abort(404, '404 Not Found. This Relation no longer exists in the database.');
		}

		return view('relations.edit', compact('collection','relation'));
	}

	public function create(Collection $collection, Relation $relation, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		return view('relations.create', compact('collection','relation'));
	}

	public function store(Collection $collection, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'relation_name' => 'required',
			'collection_id' => 'required'
		]);

		Relation::create($request->all());

		return Redirect::to('/collections/' . $collection->id . '/relations')->with('message', 'Relation created.');
	}

	public function update(Collection $collection, Relation $relation, Request $request)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'relation_name' => 'required',
			'collection_id' => 'required'
		]);

		//update relation
		$relation->update($request->all());

		return Redirect::to('/collections/' . $collection->id . '/relations')->with('message', 'Relation updated.');
	}

	public function destroy(Collection $collection, Relation $relation)
	{
		//check for the right privileges
		if (Gate::denies('edit-collection', $collection)) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//delete relation
		$relation->delete();

		return Redirect::to('/collections/' . $collection->id . '/relations')->with('message', 'Relation deleted.');
	}

	public function apiIndex()
	{
		$relations = Relation::orderBy('id', 'asc')->get();
		return response()->json($relations);
	}

	public function apiShow($id)
	{
		$relation = Relation::find($id);
		return response()->json($relation);
	}

	public function apiCollection($id)
	{
		$relations = Relation::where('collection_id', $id)->select('id','relation_name')->orderBy('relation_name', 'asc')->get();
		return response()->json($relations);
	}
}
