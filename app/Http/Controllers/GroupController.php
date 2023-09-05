<?php

namespace App\Http\Controllers;
use App\Group;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;

class GroupController extends Controller
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

		$groups = Group::orderBy('group_name', 'asc')->get();
		return view('groups.index', compact('groups'));
	}

	public function edit(Group $group)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check if id property exists
		if (!$group->id) {
			abort(404, '404 Not Found. This group no longer exists in the database.');
		}

		$group->load('users');

		return view('groups.edit', compact('group'));
	}

	public function create(Group $group)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		return view('groups.create', compact('group'));
	}

	public function store(Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'group_name' => 'required|min:3|unique:groups',
			'group_description' => 'required'
		]);

		Group::create($request->all());
		return Redirect::route('groups.index')->with('message', 'Group created');
	}

	public function update(Group $group, Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'group_name' => 'required|min:3',
			'group_description' => 'required'
		]);

		$group->update($request->all());
		return Redirect::route('groups.show', $group->slug)->with('message', 'Group updated.');
	}

	public function destroy(Group $group)
	{
 		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		$group->delete();
		return Redirect::route('groups.index')->with('message', 'Group deleted.');
	}

}
