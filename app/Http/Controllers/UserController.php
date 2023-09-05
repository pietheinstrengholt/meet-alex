<?php

namespace App\Http\Controllers;
use App\Group;
use App\Http\Controllers\Controller;
use App\Log;
use App\Section;
use App\User;
use App\UserRights;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use App\Term;

class UserController extends Controller
{
	public function index()
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		$users = User::orderBy('email', 'asc')->get();
		return view('users.index', compact('users'));
	}

	public function edit(User $user)
	{
		//check for admin permissions
		if (Gate::denies('admin') && $user->id <> Auth::user()->id) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check for admin permissions
		if (Gate::denies('admin') && (!empty($user->provider))) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check if id property exists
		if (!$user->id) {
			abort(404, '404 Not Found. This user no longer exists in the database.');
		}

		$groups = Group::orderBy('group_name', 'asc')->get();
		return view('users.edit', compact('groups','user'));
	}

	public function update(User $user, Request $request)
	{
		//check for admin permissions
		if (Gate::denies('admin') && $user->id <> Auth::user()->id) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check for admin permissions
		if (Gate::denies('admin') && (!empty($user->provider))) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//validate input form
		$this->validate($request, [
			'name' => 'required',
			'email' => 'email|required'
		]);

		$user->update($request->all());

		if (Gate::denies('admin')) {
			return redirect('/');
		} else {
			return Redirect::route('users.index')->with('message', 'User updated.');
		}
	}

	public function destroy(User $user)
	{
		//check for admin permissions
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized action.');
		}

		//find user
		$user = User::findOrFail($user->id);

		//TODO: user cannot be deleted if it owns a collection
		//assign collection owners to term owner
		$terms = Term::where('owner_id', $user->id)->get();
		if (!empty($terms)) {
			foreach ($terms as $term) {
				Term::where('id', $term->id)->update(['created_by' => $term->collection->created_by]);
			}
		}

		//delete user
		$user->delete();

		return Redirect::route('users.index')->with('message', 'User deleted.');
	}

	public function bookmarks()
	{
		//check for the right privileges
		if (!Auth::check()) {
			return redirect('/login');
		}

		//get all collections where the user has permissions on
		$collections = app('auth.manager')->getReadableCollections();

		return view('users.bookmarks', compact('collections'));
	}

	public function postbookmarks(Request $request)
	{
		//check for the right privileges
		if (!Auth::check()) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//sync collections from user_collection table
		if ($request->has('collections')) {
			Auth::user()->bookmarks()->sync($request->input('collections'));
		}

		return redirect('');
	}

	public function show(User $user)
	{
		abort(403, 'There is no page to retrieve the user details yet..');
	}

	public function password(User $user)
	{
		//check for admin permissions
		if (Gate::denies('admin') && $user->id <> Auth::user()->id) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		//check for admin permissions
		if (Gate::denies('admin') && (!empty($user->provider))) {
			abort(403, '403 Forbidden. Unauthorized action.');
		}

		return view('users.editpassword', compact('user'));
	}

	public function updatepassword(User $user, Request $request)
	{
		//validate input form
		$this->validate($request, [
			'password' => 'required|confirmed|min:6',
		]);
		if ($request->isMethod('post')) {
			//update password
			User::where('id', $user->id)->update(['password' => bcrypt($request->input('password'))]);
		}
		//return to collections page with message
		return Redirect::to('/collections/')->with('message', 'Password updated.');
	}

}
