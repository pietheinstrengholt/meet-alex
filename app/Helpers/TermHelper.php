<?php

namespace app\Helpers;
use App\Helpers\TermHelper;
use App\Term;

class TermHelper
{
	//function to make hyperlinks from urls
	public static function returnMaxId() {
		//get latest id from the database
		$max = Term::max('id');
		if (empty($max)) {
			$max = 0;
		}

		//return highest id + 1 from the terms table
		return $max + 1;
	}
}

?>
