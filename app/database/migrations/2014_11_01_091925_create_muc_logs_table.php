<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMucLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('muc_logs', function($t)
		{
			$t->increments('id')->unsigned();
			$t->integer('rest_api_user_id');
			$t->text('body');
			$t->text('muc');
			$t->boolean('ping');
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
		Schema::drop('muc_logs');
	}

}
