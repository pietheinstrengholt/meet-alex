<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Collection;
use App\Term;
use App\User;
use App\Status;
use App\Ontology;
use App\Relation;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use App\Group;
use DB;
use File;
use GuzzleHttp\Client;
use App\Helpers\TermHelper;

class ImportController extends Controller
{
	//function needed to parse Excel
	public function getNameFromNumber($num)
	{
		$numeric = ($num - 1) % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval(($num - 1) / 26);
		if ($num2 > 0) {
			return getNameFromNumber($num2) . $letter;
		} else {
			return $letter;
		}
	}

	//function needed to parse Excel
	public function getExcelColumnNumber($num)
	{
		$numeric = ($num - 1) % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval(($num - 1) / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2) . $letter;
		} else {
			return $letter;
		}
	}

	public function alexfile()
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		return view('import.alexfile');
	}

	public function alexapi()
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		return view('import.alexapi');
	}

	public function excelfile()
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		return view('import.excel');
	}

	//function to search in array for specific value
	public function searchForValue($id, $array, $argument) {
		foreach ($array as $key => $val) {
			if ($val[$argument] === $id) {
				return $key;
			}
		}
		return null;
	}

	public function postexcel(Request $request)
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		//validate input form
		$this->validate($request, [
			'collection_name' => 'required|min:3|unique:collections',
			'collection_description' => 'required',
			'excelfile' => 'required|mimes:xls,xlsx',
		]);

		//create empty arrays for error check and building up results
		$alexArray = array();
		$errors = array();

		if ($request->file('excelfile')->isValid()) {

			$validation = Excel::load($request->file('excelfile'), function ($reader) use ($request, &$errors, &$alexArray) {

				// Getting all sheets
				$reader->setReadDataOnly(true);
				$reader->ignoreEmpty();
				$sheets = $reader->get();

				//empty array for unique term names validation
				$term_names = array();

				foreach($sheets as $sheet) {

					$worksheetTitle = $sheet->getTitle();
					$arraySheet = $sheet->toArray();

					//get column and row count from imported excel
					$highestRow = count($arraySheet) + 1;
					if (array_key_exists(0,$arraySheet)) {
						$highestColumn = $this->getExcelColumnNumber(count($arraySheet[0]));
						$highestColumnIndex = count($arraySheet[0]) + 1;
					} else {
						$highestColumn = 0;
						$highestColumnIndex = 1;
					}

					//start counting unique id content
					$i = 0;

					if ($worksheetTitle == "terms") {

						if ($highestRow > 1) {

							for ($row = 1; $row <= $highestRow; ++ $row) {
								//hardcode 2 columns, otherwise the term_definition is not fetched from the sheet
								for ($column = 1; $column < 3; ++ $column) {

									//set column letter and retrieve value
									$columnLetter = $this->getExcelColumnNumber($column);
									$val = $reader->getExcel()->getSheet(0)->getCell($columnLetter . $row)->getValue();

									//1th row is the heading
									if ($row == 1 && ($column == 1 && $val != 'term_name' || $column == 2 && $val != 'term_definition')) {
										//validate if heading is correct
										array_push($errors, "Incorrect heading on sheet terms");
									}

									//check if the content is correct and populate alexArray
									if ($row > 1 && $column == 1) {
										$alexArray['terms'][$i]['id'] = $i;
										$alexArray['terms'][$i]['term_name'] = $val;
										if (in_array($val, $term_names)) {
											array_push($errors, "Dupplicate term name found in terms sheet");
											$alexArray['terms'][$i]['error'] = "1";
										}
										array_push($term_names, $val);
									}

									if ($row > 1 && $column == 2) {
										if (!empty($val)) {
											$alexArray['terms'][$i]['term_definition'] = $val;
										}
									}
								}
								//increase count
								$i++;
							}
						}
					}

					if ($worksheetTitle == "ontology") {

						if ($highestRow > 1) {

							$alexArray['relations'] = array();

							for ($row = 1; $row <= $highestRow; ++ $row) {
								for ($column = 1; $column < $highestColumnIndex; ++ $column) {

									//set column letter and retrieve value
									$columnLetter = $this->getExcelColumnNumber($column);
									$val = $reader->getExcel()->getSheet(1)->getCell($columnLetter . $row)->getValue();

									//1th row is the heading
									if ($row == 1 && ($column == 1 && $val != 'subject_name' || $column == 2 && $val != 'relation_name' || $column == 3 && $val != 'object_name')) {
										//validate if heading is correct
										array_push($errors, "Incorrect heading on sheet terms");
									}

									//get the relation and add this to the alexArray
									if ($row > 1 && $column == 2) {
										if (!$this->searchForValue($val, $alexArray['relations'], 'relation_name')) {
											$alexArray['relations'][$i]['id'] = $i;
											$alexArray['relations'][$i]['relation_name'] = $val;
											$alexArray['relations'][$i]['relation_description'] = null;
										}
										$alexArray['ontologies'][$i]['relation_id'] = $this->searchForValue($val, $alexArray['relations'], 'relation_name');
									}

									//get the subject id and validate if a term with a given id exists
									if ($row > 1 && $column == 1) {
										if ($this->searchForValue($val, $alexArray['terms'], 'term_name')) {
											$alexArray['ontologies'][$i]['subject_id'] = $this->searchForValue($val, $alexArray['terms'], 'term_name');
										} else {
											$alexArray['ontologies'][$i]['error'] = "1";
											array_push($errors, "term_name on ontologies sheet cannot be found on sheet terms");
										}

									}

									//get the object id and validate if a term with a given id exists
									if ($row > 1 && $column == 3) {
										if ($this->searchForValue($val, $alexArray['terms'], 'term_name')) {
											$alexArray['ontologies'][$i]['object_id'] = $this->searchForValue($val, $alexArray['terms'], 'term_name');
										} else {
											$alexArray['ontologies'][$i]['error'] = "1";
											array_push($errors, "term_name on ontologies sheet cannot be found on sheet terms");
										}
									}
								}
								$i++;
							}
						}
					}

				}
			});

			//exit if errors have been found
			if (!empty($errors)) {
				return view('errors.excel', compact('alexArray','errors'));
			}

			//exit if the alexArray is empty
			if (empty($alexArray['terms'])) {
				abort(400, '400 Bad Request. Incorrect Excel file. The terms sheet is empty.');
			}

			//create collection
			$collection = Collection::create(['collection_name' => $request->input('collection_name'), 'collection_description' => $request->input('collection_description'), 'public' => 1, 'workflow' => 0, 'created_by' => Auth::user()->id]);

			//use importContent to submit array and create content
			$this->importContent($collection, $alexArray);

			//return to newly created collection
			return Redirect::to('/collections/' . $collection->id)->with('message', 'Content successfully imported and added to the new Collection.');
		}
	}

	public function migration1()
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		return view('import.migration-step1');
	}

	public function postmigration1(Request $request)
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		//validate input form
		$this->validate($request, [
			'alex_url' => 'required|url',
		]);

		$client = new Client(['verify' => false]); //GuzzleHttp\Client
		$alex_url = $request->input('alex_url');
		$alex_url = rtrim($alex_url,"/");
		$alex_url = $alex_url . '/api/collections';

		$res = $client->request('GET', $alex_url);
		if ($res->getStatusCode() == "200") {
			//decode json and store to alexArray variable
			//abort if json is not valid
			if (!json_decode($res->getBody())) {
			  abort(400, 'Error. The returned formaty is an incorrect JSON format.');
			  exit();
			}

			$alexArray = json_decode($res->getBody(), true);

			return view('import.migration-step2', compact('alexArray','alex_url'));
		}
	}

	public function migration2()
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		return view('import.migration-step2');
	}


	public function postmigration2(Request $request) {

		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		//validate input form
		$this->validate($request, [
			'alex_url' => 'required|url',
			'collections' => 'required',
		]);

		$client = new Client(['verify' => false]); //GuzzleHttp\Client
		$alex_url = $request->input('alex_url');

		$res = $client->request('GET', $request->input('alex_url'));
		if ($res->getStatusCode() == "200") {

			//for each for every collection
			foreach ($request->input('collections') as $collection_id) {
				$client = new Client(['verify' => false]); //GuzzleHttp\Client
				$resCol = $client->request('GET', $request->input('alex_url') . '/' . $collection_id);
				if ($resCol->getStatusCode() == "200") {
					//decode json and store to alexArray variable
					//abort if json is not valid
					if (!json_decode($resCol->getBody())) {
					  abort(400, 'Error. The returned formaty is an incorrect JSON format.');
					  exit();
					}

					$alexArray = json_decode($resCol->getBody(), true);

					//create collection
					$collection = Collection::create(['collection_name' => $alexArray['collection_name'], 'collection_description' => $alexArray['collection_description'], 'public' => 1, 'workflow' => 0, 'created_by' => Auth::user()->id]);

					//use importContent to submit array and create content
					$this->importContent($collection, $alexArray);
				}
			}
		}
		return Redirect::to('/collections')->with('message', 'Content successfully migrated.');
	}

	public function postalexapi(Request $request)
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		//validate input form
		$this->validate($request, [
			'collection_name' => 'required|min:3|unique:collections',
			'collection_description' => 'required',
			'alex_url' => 'required|url',
		]);

		$client = new Client(['verify' => false]); //GuzzleHttp\Client

		$res = $client->request('GET', $request->input('alex_url'));
		if ($res->getStatusCode() == "200") {
			//decode json and store to alexArray variable
			//abort if json is not valid
			if (!json_decode($res->getBody())) {
			  abort(400, 'Error. The returned formaty is an incorrect JSON format.');
			  exit();
			}

			$alexArray = json_decode($res->getBody(), true);

			//create collection
			$collection = Collection::create(['collection_name' => $request->input('collection_name'), 'collection_description' => $request->input('collection_description'), 'public' => 1, 'workflow' => 0, 'created_by' => Auth::user()->id]);

			//use importContent to submit array and create content
			$this->importContent($collection, $alexArray);

			//return to newly created collection
			return Redirect::to('/collections/' . $collection->id)->with('message', 'Content successfully imported and added to the new Collection.');

		} else {
			abort(400, 'The API to the external url failed with error code: ' . $res->getStatusCode());
		}
	}

	public function postalexfile(Request $request)
	{
		if (!Auth::check()) {
			abort(403, 'Unauthorized action. You don\'t have access to this page');
		}

		//validate input form
		$this->validate($request, [
			'collection_name' => 'required|min:3|unique:collections',
			'collection_description' => 'required',
			'alexfile' => 'required',
		]);

		if ($request->file('alexfile')->isValid()) {

			//set variables
			$alexFile = $request->file('alexfile');
			$fileName = $alexFile->getClientOriginalName();
			$filePath = $alexFile->getPathName();
			$fileExtension = $alexFile->getClientOriginalExtension();

			if ($fileExtension = "ajax") {

				//get file contents from tmp path
				$contents = File::get($filePath);

				//abort if json is not valid
				if (!json_decode($contents)) {
				  abort(400, 'Error. Incorrect JSON format uploaded.');
				  exit();
				}

				//decode json and store to alexArray variable
				$alexArray = json_decode($contents, true);

				//create collection
				$collection = Collection::create(['collection_name' => $request->input('collection_name'), 'collection_description' => $request->input('collection_description'), 'public' => 1, 'workflow' => 0, 'created_by' => Auth::user()->id]);

				//use importContent to submit array and create content
				$this->importContent($collection, $alexArray);
			}

			return Redirect::to('/collections/' . $collection->id)->with('message', 'Content successfully imported and added to the new Collection.');
		}
	}

	public function importContent($collection, $alexArray)
	{
		//create empty array to store ids returned by database
		$termsMapping = array();
		$relationsMapping = array();

		//add all terms to database
		if (isset($alexArray['terms'])) {
			foreach ($alexArray['terms'] as $key => $term) {
				if (isset($term['term_name']) && isset($term['id'])) {

					//check if term_definition exists
					$term_definition = array_key_exists('term_definition', $term) ? $term['term_definition'] : "";

					//create new term
					$newTerm = Term::create([
						'id' => TermHelper::returnMaxId(),
						'collection_id' => $collection->id,
						'term_name' => $term['term_name'],
						'term_definition' => $term_definition,
						'version' => 1,
						'status_id' => 1,
						'owner_id' => Auth::user()->id,
						'created_by' => Auth::user()->id
					]);

					$termsMapping[$term['id']] = $newTerm->id;
				}
			}
		}

		//add all relations to database
		if (isset($alexArray['relations'])) {
			foreach ($alexArray['relations'] as $key => $relation) {
				if (isset($relation['relation_name']) && isset($relation['id'])) {
					$newRelation = Relation::create([
						'collection_id' => $collection->id,
						'relation_name' => $relation['relation_name'],
						'relation_description' => $relation['relation_description'],
						'created_by' => Auth::user()->id
					]);
					$relationsMapping[$relation['id']] = $newRelation->id;
				}
			}
		}

		//add all ontologies to database
		if (isset($alexArray['ontologies'])) {
			foreach ($alexArray['ontologies'] as $key => $ontology) {
				//check if at least a subject_id, relation_id and object_id are provided in the array
				if (isset($ontology['subject_id']) && isset($ontology['relation_id']) && isset($ontology['object_id'])) {
					//check if the id of the subject, object and relation exist
					if (isset($termsMapping[$ontology['subject_id']]) && isset($relationsMapping[$ontology['relation_id']]) && isset($termsMapping[$ontology['object_id']])) {
						$newRelation = Ontology::create([
							'collection_id' => $collection->id,
							'subject_id' => $termsMapping[$ontology['subject_id']],
							'relation_id' => $relationsMapping[$ontology['relation_id']],
							'object_id' => $termsMapping[$ontology['object_id']],
							'created_by' => Auth::user()->id
						]);
					}
				}
			}
		}
	}
}
