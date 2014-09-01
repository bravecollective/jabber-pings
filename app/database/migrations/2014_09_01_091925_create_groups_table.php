<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function($t)
		{
			$t->increments('id')->unsigned();
			$t->text('key');
			$t->timestamps();
		});

		// update the pings table
		Schema::table('pings', function($table)
		{
			$table->integer('group');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		Schema::drop('groups');
        Schema::table('pings', function($table)
        {
            $table->dropColumn('group');
        });
	}

}
