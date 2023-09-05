<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TermManager;
use App\Services\AuthManager;
use Schema;

class AppServiceProvider extends ServiceProvider
{
	/**
	* Bootstrap any application services.
	*
	* @return void
	*/
	public function boot()
	{
		//fix for RDS AWS environment
		Schema::defaultStringLength(191);
		view()->composer('index', 'App\Http\ViewComposers\CollectionComposer');
	}

	/**
	* Register any application services.
	*
	* @return void
	*/
	public function register()
	{
		// Term Manager
		$this->app->singleton('term.manager', function () {
			return new TermManager();
		});

		// Auth Manager
		$this->app->singleton('auth.manager', function () {
			return new AuthManager();
		});
	}
}
