<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Redirect;
use App\Collection;

class IndexController extends Controller
{
	public function index()
	{
		$collections = app('auth.manager')->getReadableCollections();
		$collections = $collections->sortBy('collection_name', SORT_NATURAL|SORT_FLAG_CASE);
		return view('index', compact('collections'));
	}

	public function meettheteam()
	{
		return view('meettheteam.index');
	}

	public function cookies()
	{
		return view('cookies.index');
	}

	public function imageupload()
	{
		return view('imageupload.image-dialog');
	}
}
