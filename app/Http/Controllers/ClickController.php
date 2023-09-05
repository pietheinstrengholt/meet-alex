<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use DB;

class ClickController extends Controller
{
	public function addClick(Request $request)
	{
		$click = \DB::table('clicks')->where([
			'visitor' => session()->getId(),
			'link' => $request->get('link_target'),
		])->first();

		if ($click) {
			DB::table('clicks')->where([
				'visitor' => session()->getId(),
				'link' => $request->get('link_target'),
			])->increment('count');
		} else {
			DB::table('clicks')->insert(array(
				'visitor' => session()->getId(),
				'link' => $request->input('link_target'),
				'count' => 0
			));
		}
	}
}
