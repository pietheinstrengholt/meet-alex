<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\DefaultRelation;
use App\User;
use Auth;
use Gate;
use App\Collection;
use Illuminate\Http\Request;
use Redirect;
class DefaultRelationController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}
  		$defaultrelations = DefaultRelation::orderBy('relation_name', 'asc')->get();
  		return view('defaultrelations.index', compact('defaultrelations'));
	}

	public function edit(DefaultRelation $defaultRelation)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}
		//check if id property exists
		if (!$defaultRelation->id) {
			abort(404, '404 Not Found. This relation no longer exists in the database.');
		}

		return view('defaultrelations.edit', compact('defaultRelation'));
	}

	public function create(DefaultRelation $defaultRelation)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		return view('defaultrelations.create', compact('defaultRelation'));
	}

	public function store(Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'relation_name' => 'required|min:3|unique:default_relation_types',
			'relation_description' => 'required'
		]);

		DefaultRelation::create($request->all());
		return Redirect::route('defaultrelations.index')->with('message', 'Relation created');
	}

	public function update(DefaultRelation $defaultRelation, Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'relation_name' => 'required|min:3',
			'relation_description' => 'required'
		]);

		$defaultRelation->update($request->all());
		return Redirect::route('defaultrelations.show', $defaultRelation->id)->with('message', 'Relation updated.');
	}

	public function destroy(DefaultRelation $defaultRelation)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}
		$defaultRelation->delete();
		return Redirect::route('defaultrelations.index')->with('message', 'Relation deleted.');
	}
}
