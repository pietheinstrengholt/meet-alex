<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
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

class ExcelController extends Controller
{
	//function to export Excel template
	public function download()
	{
		Excel::create('import_template', function($excel) {

			// Our first sheet
			$excel->sheet('terms', function($sheet) {
				$sheet->SetCellValue('A1', 'term_name');
				$sheet->getStyle('A1')->getFont()->setBold(true);
				$sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->getColumnDimension('A')->setWidth(30);
				$sheet->SetCellValue('B1', 'term_definition');
				$sheet->getStyle('B1')->getFont()->setBold(true);
				$sheet->getColumnDimension('B')->setWidth(150);
				$sheet->getStyle('B1')->getFill()->getStartColor()->setARGB('dff0d8');

				$sheet->cells('A1:B1', function($cells) {
					$cells->setBackground('#18bc9c');
				});
			});

			// Our second sheet
			$excel->sheet('ontology', function($sheet) {
				$sheet->SetCellValue('A1', 'subject_name');
				$sheet->getStyle('A1')->getFont()->setBold(true);
				$sheet->getColumnDimension('A')->setWidth(150);
				$sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->SetCellValue('B1', 'relation_name');
				$sheet->getStyle('B1')->getFont()->setBold(true);
				$sheet->getStyle('B1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->getColumnDimension('B')->setWidth(150);
				$sheet->SetCellValue('C1', 'object_name');
				$sheet->getStyle('C1')->getFont()->setBold(true);
				$sheet->getColumnDimension('C')->setWidth(150);
				$sheet->getStyle('C1')->getFill()->getStartColor()->setARGB('dff0d8');

				$sheet->cells('A1:C1', function($cells) {
					$cells->setBackground('#18bc9c');
				});
			});

		})->download('xlsx');
	}

	//function to export Excel template
	public function exportexcel($id)
	{
		$collection = Collection::with('terms','relations')->findOrFail($id);

		if (empty($collection['terms'])) {
			abort(400, '400 Bad Request. Nothing to export. No terms have been found in this Model.');
		}

		Excel::create('export_' . $collection->collection_name, function($excel) use ($collection) {

			// Our first sheet
			$excel->sheet('terms', function($sheet) use ($collection) {
				$sheet->SetCellValue('A1', 'term_name');
				$sheet->getStyle('A1')->getFont()->setBold(true);
				$sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->getColumnDimension('A')->setWidth(30);
				$sheet->SetCellValue('B1', 'term_definition');
				$sheet->getStyle('B1')->getFont()->setBold(true);
				$sheet->getColumnDimension('B')->setWidth(150);
				$sheet->getStyle('B1')->getFill()->getStartColor()->setARGB('dff0d8');

				$sheet->cells('A1:B1', function($cells) {
					$cells->setBackground('#18bc9c');
				});

				$i = 1;

				$termArray = array();

				foreach($collection->terms as $key => $term) {
					$i++;
					$sheet->setCellValueExplicit('A' . $i, $term->term_name)
					->setCellValueExplicit('B' . $i, $term->term_definition);
				}
			});

			// Our second sheet
			$excel->sheet('ontology', function($sheet) use ($collection) {
				$sheet->SetCellValue('A1', 'subject_name');
				$sheet->getStyle('A1')->getFont()->setBold(true);
				$sheet->getColumnDimension('A')->setWidth(150);
				$sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->SetCellValue('B1', 'relation_name');
				$sheet->getStyle('B1')->getFont()->setBold(true);
				$sheet->getStyle('B1')->getFill()->getStartColor()->setARGB('dff0d8');
				$sheet->getColumnDimension('B')->setWidth(150);
				$sheet->SetCellValue('C1', 'object_name');
				$sheet->getStyle('C1')->getFont()->setBold(true);
				$sheet->getColumnDimension('C')->setWidth(150);
				$sheet->getStyle('C1')->getFill()->getStartColor()->setARGB('dff0d8');

				$sheet->cells('A1:C1', function($cells) {
					$cells->setBackground('#18bc9c');
				});

				$i = 1;

				if (!empty($collection->ontologies)) {
					foreach($collection->ontologies as $ontology) {
						$i++;
						$sheet->setCellValueExplicit('A' . $i, $ontology->subject->term_name)
						->setCellValueExplicit('B' . $i, $ontology->relation->relation_name)
						->setCellValueExplicit('C' . $i, $ontology->object->term_name);
					}
				}
			});

		})->download('xlsx');
	}
}
