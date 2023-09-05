<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Term;
use App\Collection;
use App\User;
use App\Comment;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;

class CommentController extends Controller
{
	public function create(Request $request)
	{
		//validate input form
		$this->validate($request, [
			'comment' => 'required|min:3',
			'term_id' => 'required'
		]);

		//check if term exists
		$term = Term::findOrFail($request->input('term_id'));

		//insert comments
		$approval = new Comment;
		$approval->term_id = $term->id;
		$approval->comment = $request->input('comment');
		$approval->created_by = Auth::user()->id;
		$approval->save();

		return Redirect::to('/collections/' . $term->collection_id . '/terms/' . $term->id)->with('message', 'Commment added to term.');
	}

	public function delete(Request $request)
	{
		//get comment
		$comment = Comment::findOrFail($request->input('comment_id'));

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();

		if ($collections->contains($comment->term->collection)) {
			$comment->delete();
			return response()->json([
				'code' => '200',
				'message' => 'OK',
			], 200);
			exit();
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}
}
