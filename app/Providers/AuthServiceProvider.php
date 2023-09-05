<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Auth;
use App\Helpers\Settings;
use Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	* The policy mappings for the application.
	*
	* @var array
	*/
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	* Register any application authentication / authorization services.
	*
	* @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
	* @return void
	*/
	public function boot(GateContract $gate)
	{
		$this->registerPolicies($gate);

		$gate->define('admin', function ($user) {
			if ($user->role === "admin") {
				return true;
			}
		});

		$gate->define('edit-collection', function ($user, $collection) {
			if ($user->role === "admin") {
				return true;
				exit();
			}

			//check if the collection is owned by the user
			if (Auth::user()->id == $collection->created_by) {
				return true;
				exit();
			}
		});

		$gate->define('contribute-to-collection', function ($user) {
			if ($user->role === "admin") {
				return true;
				exit();
			}

			//check if user has created the collection
			//TODO: fix error below
			if (!empty($collection->created_by)) {
				if ($user->id == $collection->created_by) {
					return true;
					exit();
				}
			}

			//check if the contributor group belongsTo the collection departments
			if (Auth::user()->group) {
					if (!empty($collection->groups)) {
							if ($collection->groups->contains(Auth::user()->group)) {
									return true;
									exit();
							}
					}
			}

			//check if user is linked to the collection
			if (Auth::user()->collections->contains($collection)) {
				return true;
				exit();
			}
		});

		$gate->define('approve-terms', function ($user, $term) {
			if (Gate::allows('contribute-to-collection', $term->collection)) {
				//return true if the approve_own_changes is set to yes and user is the same
				if (Settings::get('approve_own_changes') === "yes" &&  Auth::user()->id === $term->owner_id) {
					return true;
					exit();
				}
				//return true if the user is not the same
				if (Auth::user()->id != $term->owner_id) {
					return true;
					exit();
				}
			}
		});
	}
}
