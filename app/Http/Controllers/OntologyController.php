<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Ontology;
use App\Relation;
use App\User;
use App\Term;
use App\Status;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use Validator;
use App\Collection;

class OntologyController extends Controller
{
	public function ontologyIndex()
	{
		$ontologies = Ontology::orderBy('id', 'asc')->get();
		return response()->json($ontologies);
	}

	public function ontologyGet($id)
	{
		$ontology = Ontology::with('subject')->with('object')->with('status')->with('relation')->get()->find($id);
		return response()->json($ontology);
	}

	public function ontologyCreate(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'object_id' => 'required|numeric',
			'subject_id' => 'required|numeric',
			'collection_id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//get ediable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();

		$readableCollections = app('auth.manager')->getReadableCollections();

		//check if object, subject, status and relation are valid
		$object = Term::find($request->input('object_id'));
		$subject = Term::find($request->input('subject_id'));
		$collection = Collection::find($request->input('collection_id'));
		if ($request->has('relation_id')) {
			$relation = Relation::find($request->input('relation_id'));

			//return error code if relation is not found
			if (!$relation) {
				return response()->json([
					'code' => '404',
					'message' => 'Relation not found',
				], 404);
				exit();
			}
		}

		//return error code if object is not found
		if (!$object) {
			return response()->json([
				'code' => '404',
				'message' => 'Object not found',
			], 404);
			exit();
		}

		//return error code if subject is not found
		if (!$subject) {
			return response()->json([
				'code' => '404',
				'message' => 'Subject not found',
			], 404);
			exit();
		}

		//return error code if subject is not found
		if (!$collection) {
			return response()->json([
				'code' => '404',
				'message' => 'Collection not found',
			], 404);
			exit();
		}

		//return error if user has no privileges on collection
		if (!$collections->contains($collection)) {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 404);
			exit();
		}

		//validate if a ontology with same properties does not already exist
		$check = Ontology::where('object_id', $request->input('object_id'))->where('status_id', $request->input('status_id'))->where('subject_id', $request->input('subject_id'))->where('relation_id', $request->input('relation_id'))->get();
		if ($check->count() > 0) {
			return response()->json([
				'code' => '400',
				'message' => 'Ontology with the same subject, object and status already present in the database.',
			], 400);
			exit();
		}

		//add validation if relation belongs to either the object or subject collection
		if (!($collections->contains($subject->collection) || $collections->contains($object->collection))) {
			return response()->json([
				'code' => '400',
				'message' => 'Subject and object doesnt belong to the collection.',
			], 400);
			exit();
		}

		//check if the relation_name argument is used
		if ($request->has('relation_name')) {
			//retrieve the relation by the attributes provided, or create it if it doesn't exist...
			$result = Relation::where('collection_id', $request->input('collection_id'))->where('relation_name', strtolower(trim($request->input('relation_name'))))->orderBy('relation_name', 'asc')->first();
			if (empty($result)) {
				$result = Relation::create(['collection_id' => $request->input('collection_id'), 'relation_name' => strtolower(trim($request->input('relation_name'))), 'created_by' => Auth::user()->id]);
			}
			$relation_id = $result->id;
		}

		//check if the relation_id argument is used
		if ($request->has('relation_id')) {
			$relation = Relation::find($request->input('relation_id'));
			//return error code if relation is not found
			if (!$relation) {
				return response()->json([
					'code' => '404',
					'message' => 'Relation not found',
				], 404);
				exit();
			}
			$relation_id = $relation->id;
		}

		//create and return new ontology
		$ontology = Ontology::create(['collection_id' => $request->input('collection_id'), 'object_id' => $request->input('object_id'), 'relation_id' => $relation_id, 'subject_id' => $request->input('subject_id'), 'status_id' => 1, 'created_by' => Auth::user()->id]);
		return response()->json($ontology, 201);
	}

	public function ontologyPut($id, Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'object_id' => 'required|numeric',
			'subject_id' => 'required|numeric',
			'collection_id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
			exit();
		}

		//get ontology
		$ontology = Ontology::find($id);

		//return error code if object is not found
		if (!$ontology) {
			return response()->json([
				'code' => '404',
				'message' => 'Ontology not found',
			], 404);
			exit();
		}

		//get ontology
		$collection = Collection::find($request->input('collection_id'));

		//return error code if object is not found
		if (!$collection) {
			return response()->json([
				'code' => '404',
				'message' => 'Collection not found',
			], 404);
			exit();
		}

		//get ediable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();
		$readableCollections = app('auth.manager')->getReadableCollections();

		//checkc if object, subject, status and relation are valid
		$object = Term::find($request->input('object_id'));
		$subject = Term::find($request->input('subject_id'));


		//return error code if object is not found
		if (!$object) {
			return response()->json([
				'code' => '404',
				'message' => 'Object not found',
			], 404);
			exit();
		}

		//return error code if subject is not found
		if (!$subject) {
			return response()->json([
				'code' => '404',
				'message' => 'Subject not found',
			], 404);
			exit();
		}

		//check if the user is able to read the object
		if (!$readableCollections->contains($object->collection) || !$readableCollections->contains($subject->collection)) {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action. You are not allowed to read the subject or object',
			], 403);
			exit();
		}

		if (!$collections->contains($collection)) {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action. You are not allowed to edit the collection',
			], 403);
			exit();
		}

		//check if the relation_name argument is used
		if ($request->has('relation_name')) {
			//retrieve the relation by the attributes provided, or create it if it doesn't exist...
			$result = Relation::where('collection_id', $request->input('collection_id'))->where('relation_name', strtolower(trim($request->input('relation_name'))))->orderBy('relation_name', 'asc')->first();
			if (empty($result)) {
				$result = Relation::create(['collection_id' => $request->input('collection_id'), 'relation_name' => strtolower(trim($request->input('relation_name'))), 'created_by' => Auth::user()->id]);
			}
			$relation_id = $result->id;
		}

		//check if the relation_id argument is used
		if ($request->has('relation_id')) {
			$relation = Relation::find($request->input('relation_id'));
			//return error code if relation is not found
			if (!$relation) {
				return response()->json([
					'code' => '404',
					'message' => 'Relation not found',
				], 404);
				exit();
			}
			$relation_id = $relation->id;
		}

		$ontology->subject_id = $request->input('subject_id');
		$ontology->status_id = $request->input('status_id');
		$ontology->relation_id = $relation_id;
		$ontology->object_id = $request->input('object_id');
		$ontology->archived = $request->input('archived');
		$ontology->created_by = Auth::user()->id;
		$ontology->save();

		//TODO: replace with load
		$ontology = Ontology::with('subject')->with('object')->with('status')->with('relation')->get()->find($id);
		return response()->json($ontology, 201);
	}

	public function ontologyDelete($id, Request $request)
	{
		//get private and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();
		$readableCollections = app('auth.manager')->getReadableCollections();

		//get ontology
		$ontology = Ontology::with('object','subject')->find($id);
		//return error code if term is not found
		if (!$ontology) {
			return response()->json([
				'code' => '404',
				'message' => 'Record not found',
			], 404);
			exit();
		}

		if (Gate::allows('edit-collection', $ontology->subject->collection)) {
			$ontology->delete();
			return response()->json([
				'code' => '200',
				'message' => 'OK',
			], 200);
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}
}
