<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// GUEST REQUIRED ROUTES
Route::group(array('before' => 'guest'), function()
{
	Route::get('login', array('as' => 'login', 'uses' => 'LoginController@loginView'));
	Route::post('login', array('uses' => 'LoginController@loginAction'));

	Route::get('info', array('as' => 'info', 'uses' => 'LoginController@infoAction'));
});

// LOGIN REQUIRED ROUTES
Route::group(array('before' => 'auth'), function()
{
	// Basic URLs
	Route::get('/', array('as' => 'home', 'uses' => 'PingController@listAllPingsView'));
	Route::get('history', array('as' => 'expired', 'uses' => 'PingController@listPingHistoryView'));

	Route::group(array('before' => 'edit'), function()
	{

		Route::get('new', array('as' => 'add_timer', 'uses' => 'PingController@addPingView'));
		Route::post('new', array('uses' => 'PingController@addPingAction'));
	});

	//
	Route::get('logout', array('as' => 'logout', 'uses' => 'LoginController@logoutAction'));
});
