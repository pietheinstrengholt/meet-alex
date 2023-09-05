<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Status;
use App\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;

class StatusController extends Controller
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
		$statuses = Status::orderBy('status_name', 'asc')->get();
		return view('statuses.index', compact('statuses'));
	}

	public function edit(Status $status)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}
		//check if id property exists
		if (!$status->id) {
			abort(404, '404 Not Found. This Status no longer exists in the database.');
		}

		return view('statuses.edit', compact('status'));
	}

	public function create(Status $status)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		return view('statuses.create', compact('status'));
	}

	public function store(Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'status_name' => 'required|min:3|unique:term_statuses',
			'status_description' => 'required'
		]);

		Status::create($request->all());
		return Redirect::route('statuses.index')->with('message', 'Status created');
	}

	public function update(Status $status, Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'status_name' => 'required|min:3',
			'status_description' => 'required'
		]);

		$status->update($request->all());
		return Redirect::route('statuses.show', $status->id)->with('message', 'Status updated.');
	}

	public function destroy(Status $status)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}
		$status->delete();
		return Redirect::route('statuses.index')->with('message', 'Status deleted.');
	}

	public function apiIndex()
	{
		$statuses = Status::orderBy('id', 'asc')->get();
		return response()->json($statuses);
	}

	public function apiShow($id)
	{
		$status = Status::find($id);
		return response()->json($status);
	}
}
