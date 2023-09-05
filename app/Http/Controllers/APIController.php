<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Term;
use App\TermStar;
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
use DB;
use App\Helpers\TermHelper;
use Validator;
use Event;
use App\Events\TermChanged;
use App\Events\UserBookmarkedCollection;

class APIController extends Controller
{
	public function termIndex(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'search' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collectionList = explode(',', app('auth.manager')->getReadableCollections()->implode('id', ','));

		//replace the collectionList if the collection_id argument is passed
		if ($request->has('collection_id')) {
			//check for rights, the collection_id argument passed should be in the collectionList
			if (in_array($request->input('collection_id'), $collectionList)) {
				$collectionList = array($request->input('collection_id'));
			}
		}

		if (is_numeric($request->input('limit'))) {
			$limit = $request->input('limit');
		} else {
			$limit = 10;
		}

		if (is_numeric($request->input('status_id'))) {
			$status_id = $request->input('status_id');
		} else {
			$status_id = 1;
		}

		if ($request->has('search')) {

			$terms1 = Term::whereIn('collection_id', $collectionList)->where('term_name', 'like', $request->input('search') . '%')->orderBy('term_name', 'asc')->take($limit)->get();

			//check if the first result already contains the limit
			if ($terms1->count() < $limit) {
				$terms2 = Term::whereIn('collection_id', $collectionList)->where('status_id', 1)->where('term_name', 'like', '%' . $request->input('search') . '%')->orderBy('term_name', 'asc')->take($limit)->get();
			} else {
				$terms2 = collect();
			}

			//check if the first and second results already contains the limit
			if (($terms1->count() + $terms2->count()) < $limit) {
				$terms3 = Term::whereIn('collection_id', $collectionList)->where('status_id', 1)->where('term_definition', 'like', '%' . $request->input('search') . '%')->orderBy('term_name', 'asc')->take($limit)->get();
			} else {
				$terms3 = collect();
			}

			$terms = collect();
			foreach ($terms1 as $term) {
				if ($terms->count() < $limit) {
					if (!$terms->contains($term)) {
						$terms->push($term);
					}
				}
			}
			foreach ($terms2 as $term) {
				if ($terms->count() < $limit) {
					if (!$terms->contains($term)) {
						$terms->push($term);
					}
				}
			}
			foreach ($terms3 as $term) {
				if ($terms->count() < $limit) {
					if (!$terms->contains($term)) {
						$terms->push($term);
					}
				}
			}

		} elseif ($request->has('withIds')) {
			$regex="/^[0-9,]+$/";
			//validate and match on only numbers and comma's
			if (preg_match($regex, $request->input('withIds'))) {
				$withIds = explode(',', $request->input('withIds'));
				$terms = Term::whereIn('collection_id', $collectionList)->whereIn('id', $withIds)->get();
			}
		} else {
			$terms = Term::whereIn('collection_id', $collectionList)->orderBy('term_name', 'asc')->take($limit)->get();
		}

		$result = array();
		if (!empty($terms)) {
			foreach ($terms as $term) {
				//only retrieve unfetched relation if getUnfetchedRelations argument is provided
				if ($request->has('getUnfetchedRelations')) {
					array_push($result, array(
							"id"  => round($term->id),
							"term_name"  => $term->term_name,
							"term_definition" => strip_tags(preg_replace("/^(.{250})([^\.]*\.)(.*)$/", "\\1\\2", $term->term_definition)), //TODO: stop not after 250 chars but after 50 words.
							"collection_name" => $term->collection->collection_name,
							"collection_id" => $term->collection_id,
							"value" => $term->term_name,
							"has_unfetched_relations" => $term->has_unfetched_relations,
							"tokens" => array($term->term_name)
						)
					);
				} else {
					array_push($result, array(
							"id"  => round($term->id),
							"term_name"  => $term->term_name,
							"term_definition" => strip_tags(preg_replace("/^(.{250})([^\.]*\.)(.*)$/", "\\1\\2", $term->term_definition)), //TODO: stop not after 250 chars but after 50 words.
							"collection_name" => $term->collection->collection_name,
							"collection_id" => $term->collection_id,
							"value" => $term->term_name,
							"tokens" => array($term->term_name)
						)
					);
				}
			}
		}

		return response()->json($result);
	}

	public function termGet($id, Request $request)
	{
		//get term
		$term = Term::find($id);

		//return error code if term is not found
		if (!$term) {
			return response()->json([
				'code' => '404',
				'message' => 'Record not found',
			], 404);
			exit();
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getReadableCollections();

		if ($collections->contains($term->collection)) {
			$term->load('collection', 'owner', 'objects', 'subjects');
			return response()->json($term->toArray(), 200);
			exit();
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}

	public function termPut($id, Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'term_name' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//get term
		$term = Term::find($id);

		//return error code if term is not found
		if (!$term) {
			return response()->json([
				'code' => '404',
				'message' => 'Record not found',
			], 404);
			exit();
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();

		if ($collections->contains($term->collection)) {
			Term::where('id', $term->id)->where('version', $term->version)->update(['current' => 0]);

			$termNew = Term::create([
				'id' => $term->id,
				'collection_id' => $term->collection_id,
				'term_name' => $request->input('term_name'),
				'term_definition' => $request->input('term_definition'),
				'version' => $term->version + 1,
				'status_id' => 1,
				'owner_id' => $term->owner_id,
				'created_by' => Auth::user()->id
			]);

			//fire an event that the term has been changed
			Event::dispatch(new TermChanged($term, Auth::user()));

			return response()->json($termNew, 201);
			exit();
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}

	public function tripleCreate(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'collection_id' => 'required|numeric',
			'data' => 'required',
		]);

		//set counter
		$i = 0;

		$resultArray = array();

		$result = $request->input('data');
		if (array_key_exists('terms', $result)) {
			//loop through array
			foreach ($result['terms'] as $key => $object) {

				//check if all fields are correctly filled
				if (!empty($object['subject_name']) && !empty($object['relation_name']) && !empty($object['object_name'])) {

					//check for subject term in data, else create new term
					$subjectTerm = Term::where('term_name', $object['subject_name'])->where('collection_id', $request->input('collection_id'))->first();
					if (empty($subjectTerm)) {
						$subjectTerm = Term::create([
							'id' => TermHelper::returnMaxId(),
							'collection_id' => $request->input('collection_id'),
							'term_name' => $object['subject_name'],
							'version' => 1,
							'status_id' => 1,
							'owner_id' => Auth::user()->id,
							'created_by' => Auth::user()->id
						]);
					}

					//check for relation name in data, else create new relation
					$relation = Relation::where('collection_id', $request->input('collection_id'))->where('relation_name', $object['relation_name'])->first();
					if (empty($relation)) {
						$relation = Relation::create([
							'collection_id' => $request->input('collection_id'),
							'relation_name' => $object['relation_name'],
							'created_by' => Auth::user()->id
						]);
					}

					//check for object term in data, else create new term
					$objectTerm = Term::where('term_name', $object['object_name'])->where('collection_id', $request->input('collection_id'))->first();
					if (empty($objectTerm)) {
						$objectTerm = Term::create([
							'id' => TermHelper::returnMaxId(),
							'collection_id' => $request->input('collection_id'),
							'term_name' => $object['object_name'],
							'version' => 1,
							'status_id' => 1,
							'owner_id' => Auth::user()->id,
							'created_by' => Auth::user()->id
						]);
					}

					//check for ontology, else create new one
					$ontology = Ontology::where('collection_id', $request->input('collection_id'))->where('subject_id', $subjectTerm->id)->where('relation_id', $relation->id)->where('object_id', $objectTerm->id)->first();
					if (empty($ontology)) {
						$ontology = Ontology::create([
							'collection_id' => $request->input('collection_id'),
							'subject_id' => $subjectTerm->id,
							'relation_id' => $relation->id,
							'object_id' => $objectTerm->id,
							'status_id' => 1,
							'archived' => 0
						]);
					}

					$resultArray[$i] = array("subject_id"=>$subjectTerm->id, "relation_id"=>$relation->id, "object_id"=>$objectTerm->id, "ontology_id"=>$ontology->id);
					$i++;
				}
			}
		}

		return response()->json([
			'code' => '200',
			'data' => $resultArray,
			'message' => 'All record successfully saved.',
		], 200);
		exit();
	}

	public function termCreate(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'collection_id' => 'required|numeric',
			'term_name' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();

		//checkc if collection and status is present
		$collection = Collection::find($request->input('collection_id'));

		//return error code if term is not found
		if (!$collection) {
			return response()->json([
				'code' => '404',
				'message' => 'Record not found',
			], 404);
			exit();
		}

		//check if a term with the same name is already present
		$validate = Term::where('term_name', $request->input('term_name'))->where('collection_id', $request->input('collection_id'))->where('status_id', 1)->get();
		if ($validate->count() > 0) {
			return response()->json("400 Bad Request. Term_name already present in the database.", 400);
			exit();
		}

		//create term if the user has the correct rights
		if ($collections->contains($collection)) {
			$term = Term::create([
				'id' => TermHelper::returnMaxId(),
				'collection_id' => $request->input('collection_id'),
				'term_name' => $request->input('term_name'),
				'term_definition' => $request->input('term_definition'),
				'version' => 1,
				'status_id' => 1,
				'owner_id' => Auth::user()->id,
				'created_by' => Auth::user()->id
			]);

			//return object with status 201
			return response()->json($term, 201);
			exit();
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}

	public function termDelete($id, Request $request)
	{
		//get term
		$term = Term::find($id);

		//return error code if term is not found
		if (!$term) {
			return response()->json([
				'code' => '404',
				'message' => 'Record not found',
			], 404);
			exit();
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getEditableCollections();

		if ($collections->contains($term->collection)) {
			Term::where('id', $term->id)->update(['archived' => 1]);
			Ontology::where('subject_id', $term->id)->update(['archived' => 1]);
			Ontology::where('object_id', $term->id)->update(['archived' => 1]);
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

	public function visualise(Request $request)
	{
		//error with 400 Bad Request if arguments are missing
		if (!($request->has('withIds') || $request->has('getCollection'))) {
			return response()->json([
				'code' => '400',
				'message' => 'Argument withIds or getCollection is missing',
			], 400);
			exit();
		}

		//get editable and public collections, see App\AuthService, user credentials are checked because of _token
		$collections = app('auth.manager')->getReadableCollections();

		$termArray['nodes'] = array();
		$termArray['links'] = array();

		$ontologyContainer = collect();

		//create an empty collection to build a collection containing all ontologies (relations)
		$ontologyCollection = collect();

		if ($request->has('withIds')) {
			$regex="/^[0-9,]+$/";
			//validate and match on only numbers and comma's
			if (preg_match($regex, $request->input('withIds'))) {
				$withIds = explode(',', $request->input('withIds'));
				$termsContainer = Term::whereIn('collection_id', explode(',', $collections->implode('id', ',')))->whereIn('id', $withIds)->get();
			}

			//start count for loop
			$x = 1;

			//set count to visualAmount or default to 1
			$levelsDeep = $request->input('levelsDeep', 1);

			//set scope of the collection if collectionOnly argument is provided and the withIds count is equal to 1
			if ($request->has('collectionOnly') && count(explode(',', $request->input('withIds'))) == 1) {
				$collectionIds = array($termsContainer->first()->collection_id);
			} else {
				$collectionIds = explode(',', $collections->implode('id', ','));
			}

			//loop a number of times through the relation tables based on the ids in the container
			while($x <= $levelsDeep) {
				//query ontology based on what is in the container
				if ($request->has('onlySubjects')) {
					$ontologies = Ontology::whereIn('subject_id', explode(',', $termsContainer->implode('id', ',')))->get();
				} elseif ($request->has('onlyObjects')) {
					$ontologies = Ontology::whereIn('object_id', explode(',', $termsContainer->implode('id', ',')))->get();
				} else {
					$ontologies = Ontology::whereIn('subject_id', explode(',', $termsContainer->implode('id', ',')))->orWhereIn('object_id', explode(',', $termsContainer->implode('id', ',')))->get();
				}

				/*
					if not empty push subject, object and relations found to containers
					the ontologyContainer and relationContainer are needed to later in the process to single queries for getting all relation names and other relations
				*/
				if (!empty($ontologies)) {
					foreach ($ontologies as $ontology) {
						$termsContainer->push($ontology->subject);
						$termsContainer->push($ontology->object);
					}
				}

				//increate count
				$x++;
			}

			//get all terms based on the content in the ontologycontainer
			$terms = Term::whereIn('collection_id', explode(',', $collections->implode('id', ',')))->whereIn('id', explode(',', $termsContainer->implode('id', ',')))->orderBy('term_name', 'asc')->whereIn('collection_id', $collectionIds)->get();
			$ontologies = Ontology::whereIn('subject_id', explode(',', $termsContainer->implode('id', ',')))->whereIn('object_id', explode(',', $termsContainer->implode('id', ',')))->whereIn('collection_id', explode(',', $collections->implode('id', ',')))->whereIn('collection_id', $collectionIds)->get();
		}

		if ($request->has('getCollection')) {

			//abort if collection is not numeric
			if (!is_numeric($request->input('getCollection'))) {
				return response()->json([
					'code' => '400',
					'message' => 'Invalid getCollection argument',
				], 400);
				exit();
			}

			//first try to find the collection
			$collection = Collection::find($request->input('getCollection'));

			//validate if the user has proper rights
			if (empty($collection)) {
				return response()->json([
					'code' => '404',
					'message' => 'Model not found',
				], 404);
				exit();
			}

			//validate if the user has proper rights
			if (!$collections->contains($collection)) {
				return response()->json([
					'code' => '404',
					'message' => 'Unauthorized action',
				], 404);
				exit();
			}

			//get all the for this collection
			$collection->load('terms','ontologies');
			$terms = $collection->terms;

			//TODO: move logic to Model
			//add links to terms
			if (!empty($collection->links)) {
				foreach ($collection->links as $key => $link) {
					$terms->push($link);
				}
			}

			//both the subject and object should be part of the collection
			$ontologies = $collection->ontologies;


		} //end of getCollection

		if (empty($terms)) {
			return response()->json([
				'code' => '404',
				'message' => 'Result is empty',
			], 404);
			exit();
		}

		//goal is to minimize the amount of queries and full table scans. In order to do so we create containers from where we can query as a whole
		$hasOntology = collect();

		//make the links part of the final array ready
		if (!empty($ontologies)) {
			foreach ($ontologies as $key => $ontology) {
				//an ontology is only valid if both the subject and object are found in the terms results
				if ($terms->contains($ontology->subject) && $terms->contains($ontology->object)) {
					$termArray['links'][$key]['id'] = $ontology->id;
					$termArray['links'][$key]['source'] = $ontology->subject_id;
					$termArray['links'][$key]['relation_id'] = $ontology->relation_id;
					$termArray['links'][$key]['target'] = $ontology->object_id;
					$termArray['links'][$key]['link_name'] = $ontology->relation->relation_name;

					//push the terms that have relations to the ontolycheck array
					$hasOntology->push($ontology->subject);
					$hasOntology->push($ontology->object);
				}
			}
		}

		//make the nodes part of the final array ready
		if (!empty($terms)) {

			//sort terms by term_name
			$terms = $terms->sortBy('term_name');

			//check if get getUnfetchedRelations argument if set
			if ($request->has('getUnfetchedRelations')) {

				//query ontologies based on what is in the container, using only a single query on the database
				if ($request->has('onlySubjects')) {
					$unfetchedOntologies = Ontology::whereIn('subject_id', explode(',', $terms->implode('relation_id', ',')))->get();
				} elseif ($request->has('onlyObjects')) {
					$unfetchedOntologies = Ontology::whereIn('object_id', explode(',', $terms->implode('relation_id', ',')))->get();
				} else {
					$unfetchedOntologies = Ontology::whereIn('subject_id', explode(',', $terms->implode('relation_id', ',')))->orWhereIn('object_id', explode(',', $terms->implode('relation_id', ',')))->get();
				}

				//here we create a new collection that will be used holding the terms ontologies that we not in the previous created collection
				$unfetchedCollection = collect();

				//TODO: if an ontology/relation is not found in the previous created collection push it top the unfetchedCollection
				//make an array with all the subject and object from the unfetchedCollection, this will be used to determine the has_unfetched_relations flag
				if (!empty($unfetchedCollection)) {
					foreach ($unfetchedCollection as $key => $hasUnfetchedOntology) {
						array_push($hasUnfetchedArray,$hasUnfetchedOntology->subject_id);
						array_push($hasUnfetchedArray,$hasUnfetchedOntology->object_id);
					}
				}

				//make the collection unique
				$unfetchedOntologies->unique();
			}

			//TODO: fix option hasOnlyRelations
			foreach ($terms as $key => $term) {
				$termArray['nodes'][$key]['id'] = $term->id;
				$termArray['nodes'][$key]['term_name'] = $term->term_name;
				$termArray['nodes'][$key]['term_definition'] = strip_tags($term->term_definition);
				$termArray['nodes'][$key]['collection_id'] = $term->collection_id;
				$termArray['nodes'][$key]['status_id'] = $term->status_id;
				$termArray['nodes'][$key]['collection_name'] = $term->collection->collection_name;
				//add the has_unfetched_relations element if argument is set
				if ($request->has('getUnfetchedRelations')) {
					if ($unfetchedCollection->contains($term)) {
						$termArray['nodes'][$key]['has_unfetched_relations'] = true;
					} else {
						$termArray['nodes'][$key]['has_unfetched_relations'] = false;
					}
				}
			}
		}

		//re-index nodes and links array starting to zero
		$termArray['nodes'] = array_values($termArray['nodes']);
		$termArray['links'] = array_values($termArray['links']);

		return response()->json($termArray);
	}

	public function collectionIndex()
	{
		//get all collections where the user has permissions on
		$collections = app('auth.manager')->getReadableCollections();

		return response()->json($collections->toArray(), 200);
	}

	public function collectionShow($id, Request $request)
	{
		//get all collections where the user has permissions on
		$collections = app('auth.manager')->getReadableCollections();

		$collection = Collection::with('relations','ontologies','terms','owner')->get()->find($id);

		//TODO: move logic to Model
		//add links to terms
		if (!empty($collection->links)) {
			foreach ($collection->links as $key => $link) {
				$collection->terms->push($link);
			}
		}

		//check if the user is allowed to export/view the collection
		if ($collections->contains($collection)) {
			//if download argument is provided return the collection as a downloadable alex file
			if ($request->has('download')) {
				$fileName = $collection->collection_name . ".alex";
				$headers = ['Content-type'=>'text/plain', 'Content-Disposition'=>sprintf('attachment; filename="%s"', $fileName)];
				return \Response::make($collection->toJson(), 200, $headers);
			} else {
				//else return the collection in plain json
				return response()->json($collection);
			}
		} else {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action',
			], 403);
			exit();
		}
	}

	//clone existin term to collection
	//TODO: create manager or service for cloning to cleanup below
	public function cloneTerm(Request $request)
	{
		//validate if both term_id and collection_id arguments are set
		if ($request->has('term_id') && $request->has('collection_id')) {
			$term = Term::findOrFail($request->input('term_id'));
			$collection = Collection::findOrFail($request->input('collection_id'));

			//get editable collections based on _token
			$editableCollections = app('auth.manager')->getEditableCollections();

			//validate if user is allowed to contribute to collection
			if (!$editableCollections->contains($collection)) {
				response()->json('401 Unauthorized. You are not allowed to add content to this Model, please contact your administrator.', 401);
				exit();
			}

			//check if term name already exists
			$terms = Term::where('collection_id', $request->input('collection_id'))->where('term_name', $term->term_name)->get();

			if ($terms->count()) {
				return response()->json("400 Bad Request. A term with the same name already exists in this collection.", 400);
			}

			//create new term
			$newTerm = Term::create([
				'id' => TermHelper::returnMaxId(),
				'collection_id' => $request->input('collection_id'),
				'term_name' => $term->term_name,
				'term_definition' => $term->term_definition,
				'version' => 1,
				'status_id' => 1, //published
				'owner_id' => Auth::user()->id,
				'created_by' => Auth::user()->id
			]);

			//validate if user does not try to link the term to the same collection
			if ($collection->id == $term->collection_id) {
				response()->json('401 Unauthorized. You are not allowed to link a term to the same collection.', 401);
				exit();
			}

			if ($request->has('relation_id')) {
				$relation = Relation::findOrFail($request->input('relation_id'));
				$ontology = new Ontology;
				$ontology->subject_id = $clonedTerm->id;
				$ontology->collection_id = $request->input('collection_id');
				$ontology->relation_id = $request->input('relation_id');
				$ontology->object_id = $request->input('term_id');
				$ontology->status_id = 1; //published
				$ontology->created_by = Auth::user()->id;
				$ontology->save();
			}

			//return json message
			return response()->json([
				'code' => '201',
				'message' => "Successfully cloned term into a new term with id: " . $clonedTerm->id,
				'data' => $clonedTerm
			], 201);
		}
	}

	public function linkTerm(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'term_id' => 'required|numeric',
			'collection_id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
			exit();
		}

		$term = Term::findOrFail($request->input('term_id'));
		$collection = Collection::findOrFail($request->input('collection_id'));

		//get editable collections based on _token
		$editableCollections = app('auth.manager')->getEditableCollections();

		//validate if user is allowed to contribute to collection
		if (!$editableCollections->contains($collection)) {
			response()->json('401 Unauthorized. You are not allowed to add content to this Model, please contact your administrator.', 401);
			exit();
		}

		//validate if user does not try to link the term to the same collection
		if ($collection->id == $term->collection_id) {
			response()->json('401 Unauthorized. You are not allowed to link a term to the same collection.', 401);
			exit();
		}

		//check if term name already exists
		$terms = Term::where('collection_id', $request->input('collection_id'))->where('term_name', $term->term_name)->get();

		if ($terms->count()) {
			return response()->json("400 Bad Request. A term with the same name already exists in this collection.", 400);
			exit();
		}

		//link term to collection
		$collection->links()->attach($term, ['version' => $term->version, 'created_by' => Auth::user()->id]);

		//return json message
		return response()->json([
			'code' => '201',
			'message' => "Successfully linked term to the collection"
		], 201);
	}

	public function unlinkTerm(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'term_id' => 'required|numeric',
			'collection_id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
			exit();
		}

		$term = Term::findOrFail($request->input('term_id'));
		$collection = Collection::findOrFail($request->input('collection_id'));

		//get editable collections based on _token
		$editableCollections = app('auth.manager')->getEditableCollections();

		//validate if user does not try to link the term to the same collection
		if ($collection->id == $term->collection_id) {
			response()->json('401 Unauthorized. You are not allowed to remove a relation from a term and collection using the unlink method.', 401);
			exit();
		}

		//remove link between term and collection
		$collection->links()->detach($term);

		//return json message
		return response()->json([
			'code' => '201',
			'message' => "Successfully unlinked term from the collection"
		], 201);
	}

	public function starTerm(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'term_id' => 'required|numeric',
			'star_amount' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//validate term
		$term = Term::findOrFail($request->input('term_id'));

		//remove existing rating
		TermStar::where('term_id',$term->id)->where('created_by',Auth::user()->id)->delete();

		if ($request->has('star_amount')) {
			//insert star comments
			$star = new TermStar;
			$star->term_id = $term->id;
			$star->rating = $request->input('star_amount');
			$star->created_by = Auth::user()->id;
			$star->save();
		}

		//return json message
		return response()->json([
			'code' => '201',
			'message' => 'Created. Successfully rated term.',
		], 201);
		exit();
	}

	public function sketchCreate(Request $request)
	{
		// Validate user input
		$validator = Validator::make($request->all(), [
			'collection_id' => 'required|numeric',
			'sketch_name' => 'required',
			'sketch_data' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json($validator->errors(), 422);
		}

		//retrieve collection
		$collection = Collection::find($request->input('collection_id'));

		//return error code if collection is not found
		if (!$collection) {
			return response()->json([
				'code' => '404',
				'message' => 'Collection not found'
			], 404);
			exit();
		}

		//get editable collections based on _token
		$readableCollections = app('auth.manager')->getReadableCollections();

		//validate if user is allowed to contribute to collection
		if (!$readableCollections->contains($collection)) {
			return response()->json([
				'code' => '403',
				'message' => 'Unauthorized action. You are not allowed to read this collection.',
			], 403);
			exit();
		}

		$sketch = \DB::table('user_collection_sketches')->where([
			'collection_id' => $request->input('collection_id'),
			'user_id' => Auth::user()->id,
			'sketch_name' => $request->input('sketch_name')
		])->first();

		//if sketch alreadu exists, delete it
		if ($sketch) {
			DB::table('user_collection_sketches')->where('collection_id', $request->input('collection_id'))->where('user_id', Auth::user()->id)->where('sketch_name', $request->input('sketch_name'))->delete();
		}

		//create sketch
		DB::table('user_collection_sketches')->insert(array(
			'collection_id' => $request->input('collection_id'),
			'user_id' => Auth::user()->id,
			'sketch_name' => $request->input('sketch_name'),
			'sketch_data' => $request->input('sketch_data')
		));

		//return json message
		return response()->json([
			'code' => '201',
			'message' => 'Created. Successfully stored sketch data.'
		], 201);
		exit();
	}

	public function sketchIndex(Request $request)
	{

		//check if collection_id is set
		if ($request->has('collection_id')) {

			//retrieve collection
			$collection = Collection::find($request->input('collection_id'));

			//return error code if collection is not found
			if (!$collection) {
				return response()->json([
					'code' => '404',
					'message' => 'Collection not found'
				], 404);
				exit();
			}

			//get editable collections based on _token
			$readableCollections = app('auth.manager')->getReadableCollections();

			//validate if user is allowed to contribute to collection
			if (!$readableCollections->contains($collection)) {
				return response()->json([
					'code' => '403',
					'message' => 'Unauthorized action. You are not allowed to read this collection.',
				], 403);
				exit();
			}
		}

		$sketches = \DB::table('user_collection_sketches')->where([
			'collection_id' => $request->input('collection_id'),
			'user_id' => Auth::user()->id
		])->get();

		return response()->json($sketches, 200);
		exit();
	}

	public function sketchGet($id, Request $request)
	{
		$sketch = \DB::table('user_collection_sketches')->where([
			'id' => $id,
			'user_id' => Auth::user()->id
		])->first();

		//return error code if collection is not found
		if (!$sketch) {
			return response()->json([
				'code' => '404',
				'message' => 'Sketch not found'
			], 404);
			exit();
		}

		return response()->json($sketch, 200);
		exit();
	}

	public function sketchCollection(Collection $collection, Request $request)
	{
		//check if id property exists
		if (!$collection->id) {
			return response()->json([
				'code' => '404',
				'message' => 'This collection no longer exists in the database.'
			], 404);
			exit();
		}

		$personalSketch = \DB::table('user_collection_sketches')->where([
			'collection_id' => $collection->id,
			'user_id' => Auth::user()->id
		])->first();

		if ($personalSketch) {
			return response()->json($personalSketch, 200);
			exit();
		} else {
			$collectionSketch = \DB::table('user_collection_sketches')->where([
				'collection_id' => $collection->id,
				'user_id' => $collection->created_by
			])->first();

			if ($collectionSketch) {
				return response()->json($collectionSketch, 200);
				exit();
			} else {
				return response()->json([
					'code' => '404',
					'message' => 'No sketches not found.'
				], 404);
				exit();
			}
		}
	}

	public function sketchDelete($id, Request $request)
	{
		//get sketch based on id
		$sketch = \DB::table('user_collection_sketches')->where([
			'id' => $id,
			'user_id' => Auth::user()->id
		])->first();

		//exit if sketch is not found
		if (empty($sketch)) {
			return response()->json([
				'code' => '404',
				'message' => 'Sketch not found.'
			], 404);
		}

		//delete sketch
		$sketch = \DB::table('user_collection_sketches')->where([
			'id' => $id,
			'user_id' => Auth::user()->id
		])->delete();

		//return json message
		return response()->json([
			'code' => '200',
			'message' => 'Deleted. Successfully deleted sketch data.'
		], 200);
		exit();
	}
}
