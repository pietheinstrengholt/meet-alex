<?php

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Auth;
use App\Collection;
use Gate;
use App\User;

class CollectionComposer {
	public function compose(View $view) {
		//only non guests will see all the collection
		if (Gate::allows('contributor')) {
			$view->with('collection', Collection::where('parent_id', null)->orWhere(function ($query) {
				$query->where('parent_id', 0);
			})->orderBy('collection_name', 'asc')->get());
		} else {
			$view->with('collection', Collection::where('parent_id', null)->where('public', 1)->orWhere(function ($query) {
				$query->where('parent_id', 0)->where('public', 1);
			})->orderBy('collection_name', 'asc')->get());
		}
	}
}
