<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertAdminScript extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		// Insert first admin user
		DB::table('users')->insert(
			array(
				'name' => 'Admin',
				'email' => 'admin@meet-alex.org',
				'role' => 'admin',
				'password' => bcrypt('Password123')
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
		//
	}
}
