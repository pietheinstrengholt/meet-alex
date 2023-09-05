<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// CORS, see https://blog.nikhilben.com/2015/09/02/laravel5-rest-api-and-cors/
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Credentials: true');

// Home index page
Route::get('/', 'IndexController@index');
Route::get('/meettheteam', 'IndexController@meettheteam');
Route::get('/cookies', 'IndexController@cookies');

// Auth
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

// Provide controller methods with object instead of ID
Route::model('terms', 'Term');
Route::model('collections', 'Collection');
Route::model('relations', 'Relation');
Route::model('defaultrelations', 'DefaultRelation');
Route::model('statuses', 'Status');
Route::model('groups', 'Group');
Route::model('users', 'User');

Route::bind('terms', function($value, $route) {
	return App\Term::whereId($value)->first();
});

Route::bind('collections', function($value, $route) {
	return App\Collection::whereId($value)->first();
});

Route::bind('relations', function($value, $route) {
	return App\Relation::whereId($value)->first();
});

Route::bind('defaultrelations', function($value, $route) {
	return App\DefaultRelation::whereId($value)->first();
});

Route::bind('statuses', function($value, $route) {
	return App\Status::whereId($value)->first();
});

Route::bind('groups', function($value, $route) {
	return App\Group::whereId($value)->first();
});

Route::bind('users', function($value, $route) {
	return App\User::whereId($value)->first();
});

// Custom routes
Route::get('/terms', 'CollectionController@allTerms');
Route::get('/collections/{collection}/share', ['middleware' => 'auth', 'uses' => 'CollectionController@share']);
Route::get('/collections/{collection}/collaborate', ['middleware' => 'auth', 'uses' => 'CollectionController@collaborate']);
Route::get('/collections/{collection}/bulkcreate', ['middleware' => 'auth', 'uses' => 'TermController@bulkcreate']);
Route::get('/collections/{collection}/grant/{user}', ['middleware' => 'auth', 'uses' => 'CollectionController@grant']);
Route::post('/collections/{collection}/postshare', ['middleware' => 'auth', 'uses' => 'CollectionController@postshare']);
Route::get('/collections/{collection}/terms/{term}/trail', ['middleware' => 'auth', 'uses' => 'TermController@trail']);
Route::get('/collections/{collection}/terms/{term}/relink', ['middleware' => 'auth', 'uses' => 'TermController@relink']);
Route::post('/collections/{collection}/terms/{term}/submit', ['middleware' => 'auth', 'uses' => 'TermController@submit']);
Route::post('/collections/{collection}/terms/{term}/updateLink', ['middleware' => 'auth', 'uses' => 'TermController@updateLink']);
Route::get('/users/bookmarks', ['middleware' => 'auth', 'uses' => 'UserController@bookmarks']);

// Model routes...
Route::resource('collections', 'CollectionController');
Route::resource('collections.terms', 'TermController');
Route::resource('collections.relations', 'RelationController');
Route::resource('defaultrelations', 'DefaultRelationController');
Route::resource('statuses', 'StatusController');
Route::resource('groups', 'GroupController');
Route::resource('users', 'UserController');

// rest api's for terms
Route::get('/api/terms', 'APIController@termIndex');
Route::get('/api/search', 'SearchController@searchApi');
Route::post('/api/terms', 'APIController@termCreate');
Route::post('/api/triples', 'APIController@tripleCreate');
Route::get('/api/terms/{id}', 'APIController@termGet');
Route::put('/api/terms/{id}', 'APIController@termPut');
Route::delete('/api/terms/{id}', 'APIController@termDelete');

// dedicated api for visualisation
Route::get('/api/visualise', 'APIController@visualise');

// dedicated api for logon / registration
Route::post('/api/login', 'AuthenticateController@login');
Route::post('/api/register', 'AuthenticateController@register');

//api to clone & link term
Route::post('/api/terms/clone', 'APIController@cloneTerm');
Route::post('/api/terms/link', 'APIController@linkTerm');
Route::post('/api/terms/unlink', 'APIController@unlinkTerm');

//api to star term
Route::post('/api/terms/star', 'APIController@starTerm');

// rest api's for collections
Route::get('/api/collections', 'APIController@collectionIndex');
Route::get('/api/collections/{id}', 'APIController@collectionShow');

// rest api's for ontologies
Route::get('/api/ontologies', 'OntologyController@ontologyIndex');
Route::post('/api/ontologies', 'OntologyController@ontologyCreate');
Route::get('/api/ontologies/{id}', 'OntologyController@ontologyGet');
Route::put('/api/ontologies/{id}', 'OntologyController@ontologyPut');
Route::delete('/api/ontologies/{id}', 'OntologyController@ontologyDelete');

// rest api's for relations
Route::post('/api/sketches', 'APIController@sketchCreate');
Route::get('/api/sketches', 'APIController@sketchIndex');
Route::get('/api/sketches/{id}', 'APIController@sketchGet');
Route::get('/api/collections/{collection}/sketches', 'APIController@sketchCollection');
Route::delete('/api/sketches/{id}', 'APIController@sketchDelete');

// rest api's for relations
Route::get('/api/relations', 'RelationController@apiIndex');
Route::get('/api/relations/{id}', 'RelationController@apiShow');

// rest api's for statuses
Route::get('/api/statuses', 'StatusController@apiIndex');
Route::get('/api/statuses/{id}', 'StatusController@apiShow');

//sumbit comments for terms
Route::post('/collections/{collection_id}/terms/{term_id}', 'CommentController@create');
//api to delete a comment
Route::post('/api/deletecomment', 'CommentController@delete');

//api to retrieve relations for clone pop-up
Route::get('/api/relations/collection/{id}', 'RelationController@apiCollection');

// api to quickly bookmark collections
Route::post('/api/users/bookmark', 'CollectionController@bookmark');

// Settings
Route::get('settings', ['middleware' => 'auth', 'uses' => 'SettingController@index']);
Route::post('/settings', 'SettingController@store');

// Search routes...
Route::post('/advancedsearch', 'SearchController@search');
Route::get('/search', 'SearchController@search');
Route::post('/search', 'SearchController@search');

// Excel
Route::get('uploadexcel', ['middleware' => 'auth', 'uses' => 'ExcelController@upload']);
Route::get('downloadexcel', ['middleware' => 'auth', 'uses' => 'ExcelController@download']);
Route::get('/collections/{collection}/excel', 'ExcelController@exportexcel');

//AlexFile
Route::get('/import/alexfile', ['middleware' => 'auth', 'uses' => 'ImportController@alexfile']);
Route::get('/import/alexapi', ['middleware' => 'auth', 'uses' => 'ImportController@alexapi']);
Route::get('/import/excel', ['middleware' => 'auth', 'uses' => 'ImportController@excelfile']);
Route::post('postalexfile', ['middleware' => 'auth', 'uses' => 'ImportController@postalexfile']);
Route::post('postalexapi', ['middleware' => 'auth', 'uses' => 'ImportController@postalexapi']);
Route::post('postexcel', ['middleware' => 'auth', 'uses' => 'ImportController@postexcel']);

Route::get('/import/migration1', ['middleware' => 'auth', 'uses' => 'ImportController@migration1']);
Route::get('/import/migration2', ['middleware' => 'auth', 'uses' => 'ImportController@migration2']);
Route::post('/import/migration1', ['middleware' => 'auth', 'uses' => 'ImportController@postmigration1']);
Route::post('migration2', ['middleware' => 'auth', 'uses' => 'ImportController@postmigration2']);

//OWL
Route::get('uploadowl', ['middleware' => 'auth', 'uses' => 'OWLController@upload']);
Route::post('postowl', ['middleware' => 'auth', 'uses' => 'OWLController@postowl']);

//Post bookmarks
Route::post('/users/postbookmarks', ['middleware' => 'auth', 'uses' => 'UserController@postbookmarks']);

//image upload
Route::get('/imageupload', 'IndexController@imageupload');
Route::post('/imageupload', 'TermController@imageUpload');

// OAuth Routes
Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

// Click tracking / counter
Route::post('/api/track-click', 'ClickController@addClick');

//user password
Route::get('users/{user}/password', ['middleware' => 'auth', 'uses' => 'UserController@password']);
Route::post('users/{user}/updatepassword', ['middleware' => 'auth', 'uses' => 'UserController@updatepassword']);

//github webhook
Route::post('/github/webhook', 'WebHookController@webhook');
