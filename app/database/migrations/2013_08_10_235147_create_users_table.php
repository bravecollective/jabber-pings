<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('users', function($t)
		{
			$t->increments('id')->unsigned();
			$t->string('username', 64);
			$t->string('password', 64);
			$t->string('email', 255);
			$t->integer('status');
			$t->integer('permission');
			$t->timestamps();
		});

		Schema::create('api_users', function($t)
		{
			$t->increments('id')->unsigned();
			$t->string('token', 64);
			$t->string('character_name', 128);
			$t->integer('alliance_id');
			$t->text('alliance_name');
			$t->text('tags');
			$t->integer('status');
			$t->integer('permission');
			$t->timestamps();
		});

		Schema::create('pings', function($t)
		{
			$t->increments('id')->unsigned();
			$t->text('message');
			$t->integer('user_id');
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
		//
		Schema::drop('users');
		Schema::drop('api_users');
		Schema::drop('timers');
	}

}