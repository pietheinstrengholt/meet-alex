<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertSettingsScript extends Migration
{
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		// Insert welcome message on homescreen
		$main_message1 = DB::table('settings')->where('config_key', 'main_message1')->first();

		if (empty($main_message1)) {
			DB::table('settings')->insert(
				array(
					'config_key' => 'main_message1',
					'config_value' => 'meet-Alex'
				)
			);
		}

		$main_message2 = DB::table('settings')->where('config_key', 'main_message2')->first();

		if (empty($main_message2)) {
			DB::table('settings')->insert(
				array(
					'config_key' => 'main_message2',
					'config_value' => 'Project meet-Alex - Business Information Model'
				)
			);
		}

		$administrator_email = DB::table('settings')->where('config_key', 'administrator_email')->first();

		if (empty($administrator_email)) {
			DB::table('settings')->insert(
				array(
					'config_key' => 'administrator_email',
					'config_value' => 'admin@meet-alex.org'
				)
			);
		}
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
