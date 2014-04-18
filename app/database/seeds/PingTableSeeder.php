<?php
class PingTableSeeder extends Seeder {

	public function run()
	{
		DB::table('pings')->delete();
	}
}