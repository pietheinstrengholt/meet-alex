<?php

namespace app\Helpers;
use App\Helpers\Format;
use App\User;
use App\Term;

class Format
{
	//function to make hyperlinks from urls
	public static function formatUrlsInText($text) {
		return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a target="_blank" href="$1">$1</a>', $text);
	}

	public static function addTermLinks($text, $collection_id) {
		//retrieve words from database
		$words = Term::where('collection_id', $collection_id)->where('archived', 0)->get();

		//limit the function if the amount of terms in the collection is more than 1000
		if ($words->count() > 1000) {
			return $text;
			exit();
		}

		//build dictionary with values the replacements
		$array_of_words = array();
		if (!empty($words)) {
			foreach ($words as $word) {
				array_push($array_of_words,$word->term_name);
			}
		}
		//use preg_replace_callback to not loose case

		//TODO: return term_id instead of using search function
		$pattern = '#(?<=^|\W)('. implode('|', array_map('preg_quote', $array_of_words)) . ')(?=$|\W)#i';
		$callback = function ($match) use ($collection_id) {
		    return "<a class='bim-link' href=" . url('search') . "?advanced-search=no&search=" . urlencode($match[0]) . ">" . $match[0] . "</a>";
		};
		return preg_replace_callback($pattern, $callback, $text);
	}

	public static function setting($input) {
		$setting = Setting::where('config_key', $input)->first();
		if (!empty($setting)) {
			return $setting->config_value;
		}
	}

	public static function contentAdjust($input, $collection_id) {
		$output = self::formatUrlsInText($input);
		$output = self::addTermLinks($output, $collection_id);
		return $output;
	}

	public static function highlightInput($input1, $input2) {
		return str_ireplace($input1, "<strong>$input1</strong>", $input2);
	}

	public static function returnSearch($query, $str, $wordcount) {
		$explode = explode($query, $str);
		$result = null;
		//if explode count is one the query was not found
		if (count($explode) == 1) {
			$result = implode(' ', array_slice(str_word_count($explode[0], 2), -$wordcount, $wordcount)) . " ";
		}
		//if explode count is more than one the query was found at least one time
		if (count($explode) > 1) {
			//check for if the string begins with the query
			if (!empty($explode[0])) {
				$result =  "..." . implode(' ', array_slice(str_word_count($explode[0], 2), -$wordcount, $wordcount)) . " ";
			}

			$result = $result . $query;

			if (!empty($explode[1])) {
				$result = $result . " " . implode(' ', array_slice(str_word_count($explode[1], 2), 0, $wordcount)) . "...";
			}
		}
		//return result
		return $result;
	}

	public static function returnLetters($terms) {
		//create an array with all first letters from all terms in the database, used for pagination
		$letters = array();
		if (!empty($terms)) {
			$terms = $terms->sortBy('term_name');
			foreach ($terms as $term) {
				if ($term) {
					$letter = substr(strtoupper($term->term_name), 0, 1);
					//format letters that start with ctype a different way
					if (!ctype_alpha($letter)) {
						$letter = "[0-9]";
					}
					array_push($letters,$letter);
				}
			}
			$letters = array_unique($letters);
		}

		return $letters;
	}
}

?>
