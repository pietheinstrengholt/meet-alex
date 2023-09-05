<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
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
use DateTime;
use File;
use App\Helpers\TermHelper;

class OWLController extends Controller
{
	public $concept; //name-identifier (slug) for a concept referenced in the request URL
	public $conceptFound; //boolean, if the requested concept identifier was found in the ontology
	public $conceptLabel; //label for the active concept, if found.
	public $conceptType; //false if not found, or class or property
	public $OWLfile; //filename for the OWL ontology
	public $xml; //simple xml of the ontology
	public $owlArray; //array of the full OWL ontology

	const labelAbbrevIRI = "rdfs:label";
	const commentAbbrevIRI = "rdfs:comment";
	const definedByAbbrevIRI = "rdfs:isDefinedBy";
	const objectPropRange = "ObjectPropertyRange";

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function upload()
	{
		if (!Auth::check()) {
			abort(403, '403 Forbidden. Unauthorized action. You don\'t have access to this page');
		}

		$collections = app('auth.manager')->getEditableCollections();

		return view('owl.upload', compact('collections'));
	}

	//construct a PHP array from the OWL ontology, easier to use for displaying
	function OWLtoArray($xml)
	{
		 if ($xml) {
				$nameSpaceArray = $this->nameSpaces();
				foreach ($nameSpaceArray as $prefix => $uri) {
					 @$xml->registerXPathNamespace($prefix, $uri);
				}
				$owlArray = array();

				$ontologyAnnotations = array();

				//get xml:base
				foreach ($xml->xpath("/owl:Ontology/@ontologyIRI") as $xpathResult) {
					$owlArray["ontologyIRI"] = (string) $xpathResult;
				}

				foreach ($xml->xpath("/owl:Ontology/owl:AnnotationAssertion") as $assertion) {
					$nameSpaceArray = $this->nameSpaces();
					foreach ($nameSpaceArray as $prefix => $uri) {
						@$assertion->registerXPathNamespace($prefix, $uri);
					}
					$prop	 = false;
					$propVal = false;
					foreach ($assertion->xpath("owl:AnnotationProperty/@abbreviatedIRI") as $xpathResult) {
						$prop = (string) $xpathResult;
					}
					foreach ($assertion->xpath("owl:Literal") as $xpathResult) {
						$propVal = (string) $xpathResult;
					}

					if ($prop != false && $propVal != false) {
					//use IRI element to set the key in array
					$key = (string) $assertion->IRI;
					//filter out IRI classes and references to w3.org
					if ($prop == "rdfs:label" || $prop == "rdfs:comment" || $prop == "rdfs:isDefinedBy") {
							$ontologyAnnotations[$key][$prop] = array();
						array_push($ontologyAnnotations[$key][$prop], $propVal);
					}
				}
			}

			$owlArray["ontology"] = $ontologyAnnotations;

			$SubClassOf = array();
			$rootParents = array();

			//create a list with unique elements
			$classes = array();
			foreach ($xml->xpath("//owl:Declaration/owl:Class/@IRI") as $key => $xpathResult) {
				$class = (string) $xpathResult;
				$classes[$class] = array();
			}

			//get SubClassOf
			foreach ($xml->xpath("//owl:SubClassOf") as $key => $SubClassResult) {
				//first object in the class in the parent
				$SubClassChild = null;
				foreach($SubClassResult->Class[0]->attributes() as $element => $child) {
					if ($element == "IRI") {
						if (array_key_exists((string) $child, $classes)) {
							$SubClassChild = (string) $child;
						}
					}
				}

				//second object in the class in the parent
				if ($SubClassResult->Class[1]) {
					foreach($SubClassResult->Class[1]->attributes() as $element => $parent) {
						if ($element == "IRI") {
							if (array_key_exists((string) $parent, $classes)) {
								$SubClassParent = (string) $parent;
							}
						}
						if ($element == "abbreviatedIRI") {
							if (array_key_exists((string) $parent, $classes)) {
								$SubClassParent = (string) $parent;
							}
							if (!array_key_exists((string) $child, $rootParents)) {
								array_push($rootParents, (string) $child);
							}
						}
					}
				}

				//set parent and child
				if (!empty($SubClassParent) && !empty($SubClassChild)) {
					$SubClassOf[$key]['parent'] = $SubClassParent;
					$SubClassOf[$key]['child'] = $SubClassChild;
				}

				$owlArray["subclasses"] = $SubClassOf;
				$owlArray["root"] = $rootParents;
			}

			$properties = array();
			foreach ($xml->xpath("//owl:Declaration/owl:ObjectProperty/@IRI") as $xpathResult) {
				$property = (string) $xpathResult;
				$properties[$property] = array();
			}

			$propertyAnnotations = array();
			foreach ($properties as $propKey => $propArray) {
				foreach ($xml->xpath("//owl:AnnotationAssertion[owl:IRI[text() = '$propKey']]") as $assertionIRI) {
					$nameSpaceArray = $this->nameSpaces();
					foreach ($nameSpaceArray as $prefix => $uri) {
						@$assertionIRI->registerXPathNamespace($prefix, $uri);
					}
					foreach ($assertionIRI->xpath("owl:AnnotationProperty/@abbreviatedIRI") as $xpathResult) {
						$prop = (string) $xpathResult;
					}
					foreach ($assertionIRI->xpath("owl:Literal") as $xpathResult) {
						$propVal = (string) $xpathResult;
					}
					$propertyAnnotations[$propKey][] = array(
						$prop => $propVal
					);
				}

				foreach ($xml->xpath("//owl:ObjectPropertyRange[owl:ObjectProperty[@IRI = '$propKey']]") as $range) {
					$nameSpaceArray = $this->nameSpaces();
					foreach ($nameSpaceArray as $prefix => $uri) {
						@$range->registerXPathNamespace($prefix, $uri);
					}
					foreach ($range->xpath("owl:Class/@IRI") as $xpathResult) {
						$rangeIRI = (string) $xpathResult;

						if (!array_key_exists($rangeIRI, $classes)) {
							 $classes[$rangeIRI] = array();
						}

						$propertyAnnotations[$propKey][] = array(
							 self::objectPropRange => $rangeIRI
						);
					}
				}
			}

			$classAnnotations = array();
			foreach ($classes as $classKey => $classArray) {
				foreach ($xml->xpath("//owl:AnnotationAssertion[owl:IRI[text() = '$classKey']]") as $assertionIRI) {
					$nameSpaceArray = $this->nameSpaces();
					foreach ($nameSpaceArray as $prefix => $uri) {
						@$assertionIRI->registerXPathNamespace($prefix, $uri);
					}
					foreach ($assertionIRI->xpath("owl:AnnotationProperty/@abbreviatedIRI") as $xpathResult) {
						$prop = (string) $xpathResult;
					}
					foreach ($assertionIRI->xpath("owl:Literal") as $xpathResult) {
						$propVal = (string) $xpathResult;
					}
					$classAnnotations[$classKey][] = array(
						$prop => $propVal
					);
				}
			}

			$owlArray["classes"] = $classAnnotations;
			$owlArray["properties"] = $propertyAnnotations;

			if ($this->concept) {
				//checks to see if the requested concept actually exists in the ontology
				$this->conceptFound = false;
				$slashConcept = "/" . $this->concept;
				if (array_key_exists($this->concept, $classAnnotations)) {
					$this->conceptFound = true;
					$this->conceptType = "class";
					$this->activeConceptLabel($this->concept, $classAnnotations);
				} elseif (array_key_exists($this->concept, $propertyAnnotations)) {
					$this->conceptFound = true;
					$this->conceptType = "property";
					$this->activeConceptLabel($this->concept, $propertyAnnotations);
				} elseif (array_key_exists($slashConcept, $classAnnotations)) {
					$this->concept		= $slashConcept;
					$this->conceptFound = true;
					$this->conceptType = "class";
					$this->activeConceptLabel($slashConcept, $classAnnotations);
				} elseif (array_key_exists($slashConcept, $propertyAnnotations)) {
					$this->concept		= $slashConcept;
					$this->conceptFound = true;
					$this->conceptType = "property";
					$this->activeConceptLabel($slashConcept, $propertyAnnotations);
				}
			}

			return $owlArray;
		}
	}

	public function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}


	 //get the label for the active concept
	 public function activeConceptLabel($actConcept, $conceptArray)
	 {
		 if (!$this->conceptLabel) {
				$actConArray = $conceptArray[$actConcept];
				foreach ($actConArray as $annoationArray) {
					 if (array_key_exists(self::labelAbbrevIRI, $annoationArray)) {
						$this->conceptLabel = $annoationArray[self::labelAbbrevIRI];
					}
				}
		}
	}

	 public function nameSpaces()
	 {
		 $nameSpaceArray = array(
				"owl" => "http://www.w3.org/2002/07/owl#",
				// "base" => set base url as found in xml
				"rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
				"xsd" => "http://www.w3.org/2001/XMLSchema#",
				"rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"xml" => "http://www.w3.org/XML/1998/namespace"
		 );

		 return $nameSpaceArray;
	}

	 //get the owl file, either from a local directory or direct from the repository
	 public function getOwlFile($useLocal = false)
	 {
		 $xmlString = false;
		 if ($useLocal) {
				$sFilename = self::localOntologyDirectory . $this->OWLfile;
				@$xmlString = $this->loadFile($sFilename);
				if (!$xmlString) {
					$host	= OpenContext_OCConfig::get_host_config();
					$sFileURL = $host . self::baseLocalRepositoryURI . $this->OWLfile;
					@$xmlString = file_get_contents($sFileURL);
				}
		} else {
				$sFileURL = self::BaseRawRepositoryHome . $this->OWLfile;
				@$xmlString = file_get_contents($sFileURL);
				if (!$xmlString) {
					$host	 = OpenContext_OCConfig::get_host_config();
					$sFileURL = $host . self::baseLocalRepositoryURI . $this->OWLfile;
					@$xmlString = file_get_contents($sFileURL);
				}
		}
		 return $xmlString;
	}

	public function postowl(Request $request)
	{
		//validate input form
		$this->validate($request, [
			'owl' => 'required|mimes:xml,owl',
			'collection_id' => 'required|numeric'
		]);

		$collection = Collection::findOrFail($request->input('collection_id'));

		if ($collection->terms->count()) {
			abort(400, '400 Bad Request. Terms for the selected collection are already present. Select an empty Collection or remove all the terms first.');
		}

		//create empty arrays for build structure and for validation
		$results = array();
		$errors = array();

		if ($request->file('owl')) {

			//upload image with random string
			$file = $request->file('owl');
			$extension = $file->getClientOriginalExtension();
			$filename = $file->getClientOriginalName();

			//see detailed information about taxonomy: http://www.ibm.com/support/knowledgecenter/en/SSWLGF_8.5.0/com.ibm.sr.doc/rwsr_configrn_classifications03.html

			$validExtensions = array("xml", "owx");

			//validate if file has the right extension
			if (!in_array(strtolower($extension), $validExtensions)) {
				abort(400, '400 Bad Request. The file extension type is unknown. Please use a file with the extension owx (OWL2).');
			}

			//create files upload folder, if not exists
			if (!file_exists(storage_path() . '/tmp/')) {
				mkdir(storage_path() . '/tmp/', 0777, true);
			}

			//generate rondom key for unique file location
			$random = str_random(10);

			//Move file to files folder
			$file->move(storage_path() . '/tmp/', $random . '.' . $extension);
			$location = storage_path() . '/tmp/' . $random . '.' . $extension;

			$xml = new \SimpleXmlElement(File::get($location));

			$result = $this->OWLtoArray($xml);

			$arrayFinal = array();

			if (!empty($result)) {
				$i = 1;
				foreach ($result['ontology'] as $key => $item) {
					if (array_key_exists('rdfs:label', $item)) {
						$arrayFinal['termsKeys'][$key] = $i;
						$arrayFinal['terms'][$i]['term_name'] = $item['rdfs:label'][0];
						//add comment to array if set
						if (array_key_exists('rdfs:isDefinedBy', $item)) {
							$arrayFinal['terms'][$i]['term_definition'] = $item['rdfs:isDefinedBy'][0];
						} elseif (array_key_exists('rdfs:comment', $item)) {
							$arrayFinal['terms'][$i]['term_definition'] = $item['rdfs:comment'][0];
						}
						//check if element is in root
						if (in_array($key, $result['root'])) {
							$arrayFinal['terms'][$i]['root'] = 1;
						}
						$i++;
					}
				}

				$i = 1;
				foreach ($result['subclasses'] as $key => $item) {
					$parentKey = $item['parent'];
					$childKey = $item['child'];
					if (array_key_exists($parentKey, $arrayFinal['termsKeys']) && array_key_exists($childKey, $arrayFinal['termsKeys'])) {
						$arrayFinal['relations'][$i]['object_id'] = $arrayFinal['termsKeys'][$parentKey];
						$arrayFinal['relations'][$i]['subject_id'] = $arrayFinal['termsKeys'][$childKey];
						$i++;
					}
				}
			}

			foreach ($arrayFinal['terms'] as $key => $term) {

				//create new term
				$newTerm = Term::create([
					'id' => TermHelper::returnMaxId(),
					'collection_id' => $request->input('collection_id'),
					'term_name' => $term['term_name'],
					'term_definition' => $term['term_definition'],
					'version' => 1,
					'status_id' => 1,
					'owner_id' => Auth::user()->id,
					'created_by' => Auth::user()->id
				]);

				//add term id to array
				$results['terms'][$key]['new_term'] = $newTerm->id;
			}

			//create initial relation
			//TODO: use relation names from OWL spec
			$new_relation = new Relation;
			$new_relation->collection_id = $request->input('collection_id');
			$new_relation->relation_name = "is a member";
			$new_relation->relation_description = "is a member";
			$new_relation->created_by = Auth::user()->id;
			$new_relation->save();
			//assign new term id, needed to properly assign ontology at a later stage
			$relationLastId = $new_relation->id;

			if (isset($arrayFinal['relations'])) {
				if (!empty($arrayFinal['relations'])) {
					foreach ($arrayFinal['relations'] as $key => $relation) {
						//set variables to lookup new term id's
						$subject_id = $relation['subject_id'];
						$object_id = $relation['object_id'];
						//TODO: add relation id

						//filter out the same relations
						$check = Ontology::where('subject_id', $subject_id)->where('relation_id', $relationLastId)->where('object_id', $object_id)->get();
						if ($check->isEmpty()) {
							//insert new ontology relations based on new term id's
							$ontology = new Ontology;
							$ontology->subject_id = $results['terms'][$subject_id]['new_term'];
							$ontology->relation_id = $relationLastId;
							$ontology->object_id = $results['terms'][$object_id]['new_term'];
							$ontology->status_id = 1;
							$ontology->workflow = 'active';
							$ontology->created_by = Auth::user()->id;
							$ontology->save();
						}
					}
				}
			}
		}

		return Redirect::route('collections.index')->with('message', 'OWL data successfully imported into Model.');
	}
}
