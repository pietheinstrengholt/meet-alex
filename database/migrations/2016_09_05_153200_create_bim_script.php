<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBimScript extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		Schema::create('clicks', function(Blueprint $table)
		{
			$table->string('visitor');
			$table->string('link');
			$table->integer('count');
		});

		Schema::create('collections', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id')->nullable();
			$table->string('collection_name');
			$table->text('collection_description', 65535)->nullable();
			$table->boolean('public');
			$table->boolean('receive_notifications')->default(1);
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('collection_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('collection_id')->unsigned()->index();
			$table->integer('group_id')->unsigned()->index();
			$table->timestamps();
		});

		Schema::create('collection_term', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('collection_id')->unsigned()->index();
			$table->integer('term_id')->unsigned()->index();
			$table->integer('version')->unsigned();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('default_relation_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('relation_name');
			$table->text('relation_description', 65535)->nullable();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('group_name');
			$table->string('group_description')->nullable();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('ontologies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('collection_id')->unsigned()->nullable()->index();
			$table->integer('subject_id')->unsigned()->index();
			$table->integer('relation_id')->unsigned()->index('ontologies_relation_id_foreign');
			$table->integer('object_id')->unsigned()->index();
			$table->integer('status_id')->unsigned()->default(1)->index('ontologies_status_id_foreign');
			$table->boolean('archived');
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('relation_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('collection_id')->unsigned()->index('relation_types_collection_id_foreign');
			$table->string('relation_name');
			$table->text('relation_description', 65535)->nullable();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('settings', function(Blueprint $table)
		{
			$table->string('config_key');
			$table->string('config_value');
			$table->timestamps();
		});

		Schema::create('terms', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->index();
			$table->integer('collection_id')->unsigned()->index();
			$table->boolean('current')->default(1)->index();
			$table->integer('version')->unsigned()->default(1);
			$table->boolean('archived')->default(0)->index();
			$table->string('term_name');
			$table->text('term_definition', 65535)->nullable();
			$table->integer('status_id')->unsigned()->default(0);
			$table->integer('owner_id')->unsigned();
			$table->integer('created_by')->unsigned();
			$table->timestamps();
		});

		Schema::create('term_comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('term_id')->unsigned()->index();
			$table->string('comment');
			$table->string('review')->nullable();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('term_stars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('term_id')->unsigned()->index('term_stars_term_id_foreign');
			$table->integer('rating');
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('term_statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('status_name');
			$table->text('status_description', 65535)->nullable();
			$table->integer('created_by');
			$table->timestamps();
		});

		Schema::create('user_collection_bookmarks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('collection_id')->unsigned()->index();
			$table->timestamps();
		});

		Schema::create('user_collection_sketches', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('collection_id')->unsigned()->nullable();
			$table->string('sketch_name');
			$table->text('sketch_data', 16777215)->nullable();
			$table->timestamps();
		});

		Schema::create('user_collection', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('collection_id')->unsigned()->index();
			$table->integer('created_by');
			$table->timestamps();
		});

		// Insert status content
		DB::table('term_statuses')->insert(
			array(
				'status_name' => 'Approved',
				'status_description' => 'Approved status'
			)
		);

		// Insert status content
		DB::table('term_statuses')->insert(
			array(
				'status_name' => 'Proposed',
				'status_description' => 'Proposed status'
			)
		);

		// Insert status content
		DB::table('term_statuses')->insert(
			array(
				'status_name' => 'Draft',
				'status_description' => 'Draft status'
			)
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
	{
		Schema::drop('clicks');
		Schema::drop('collections');
		Schema::drop('collection_group');
		Schema::drop('collection_term');
		Schema::drop('default_relation_types');
		Schema::drop('groups');
		Schema::drop('ontologies');
		Schema::drop('relation_types');
		Schema::drop('settings');
		Schema::drop('terms');
		Schema::drop('term_comments');
		Schema::drop('term_stars');
		Schema::drop('term_statuses');
		Schema::drop('user_collection_bookmarks');
		Schema::drop('user_collection_sketches');
		Schema::drop('user_collection');
	}
}
