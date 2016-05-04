<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestApiUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rest_api_users', function($t)
		{
			$t->increments('id')->unsigned();
			$t->integer('user_id');
			$t->text('app_name');
			$t->text('key_id');
			$t->text('key_secret');
			$t->boolean('active');
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rest_api_users');
	}

}
