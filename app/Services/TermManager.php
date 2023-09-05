<?php
namespace App\Services;
use App\Term;
use App\Helpers\TermHelper;
use App\Relation;
use App\Ontology;
use App\Http\Requests\CreateTermRequest;
use Auth;

class TermManager {

	public function create(CreateTermRequest $request) {

		try {
			//create term first version
			$term = Term::create([
				'id' => TermHelper::returnMaxId(),
				'collection_id' => $request->input('collection_id'),
				'term_name' => $request->input('term_name'),
				'term_definition' => $request->input('term_definition'),
				'version' => 1,
				'status_id' => $request->input('status_id'),
				'owner_id' => $request->input('owner_id'),
				'created_by' => Auth::user()->id
			]);
		} catch (\Exception $exception) {
			return $exception;
		}

		//create new term relations as received from form
		if ($request->has('Relations')) {
			foreach($request->input('Relations') as $relation) {
				if (!empty($relation['object_id']) && !empty($relation['relation_name'])) {
					//retrieve the relation by the attributes provided, or create it if it doesn't exist...
					$result = Relation::where('collection_id', $request->input('collection_id'))->where('relation_name', strtolower(trim($relation['relation_name'])))->orderBy('relation_name', 'asc')->first();
					if (empty($result)) {
						$result = Relation::create(['collection_id' => $request->input('collection_id'), 'relation_name' => strtolower(trim($relation['relation_name'])), 'created_by' => Auth::user()->id]);
					}
					Ontology::create(['collection_id' => $request->input('collection_id'), 'subject_id' => $term->id, 'relation_id' => $result->id, 'object_id' => $relation['object_id'], 'status_id' => $request->input('status_id'), 'created_by' => Auth::user()->id]);
				}
			}
		}

		return $term;
	}
}
