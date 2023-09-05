<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignScript extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up() {

		Schema::table('ontologies', function ($table) {
			$table->integer('subject_id')->unsigned()->change();
			$table->integer('object_id')->unsigned()->change();
			$table->foreign('subject_id')->references('id')->on('terms')->onDelete('cascade');
			$table->foreign('object_id')->references('id')->on('terms')->onDelete('cascade');
			$table->integer('relation_id')->unsigned()->change();
			$table->foreign('relation_id')->references('id')->on('relation_types')->onDelete('cascade');
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
		});

		Schema::table('relation_types', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
		});

		Schema::table('terms', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
		});

		Schema::table('term_comments', function ($table) {
			$table->integer('term_id')->unsigned()->change();
			$table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
		});

		Schema::table('collection_group', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
			$table->integer('group_id')->unsigned()->change();
			$table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
		});

		Schema::table('user_collection', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->change();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::table('collection_term', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
			$table->integer('term_id')->unsigned()->change();
			$table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
		});

		Schema::table('user_collection_bookmarks', function ($table) {
			$table->integer('collection_id')->unsigned()->change();
			$table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->change();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::table('user_collection_sketches', function ($table) {
			$table->integer('user_id')->unsigned()->change();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		Schema::table('term_stars', function (Blueprint $table) {
			$table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
	{
		//
	}
}
