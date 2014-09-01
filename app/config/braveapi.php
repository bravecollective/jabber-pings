<?php
return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'application-endpoint' => '',   // must be HTTPS
	'application-permission-namespace' => '', // permission namespace for your application in core

	'application-identifier' => '', // from core auth
	'local-private-key' => '',      // from local key generation
	'local-public-key'  => '',      // from local key generation, not used
	'remote-public-key' => '',      // form core auth

	'auth-edit-tags' => array(),    // form core auth
	'auth-edit-users' => array(),    // form core auth

	'ping-group-map' => array(
		'hero' => 'HERO Coalition',
		'brave' => 'Brave Collective',
		'fc' => 'HERO FCs',
	)

);