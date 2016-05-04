<?php

require_once 'vendor/autoload.php';



$user = 'pingbot';
$pass = 'secret';
$host = 'bot.bravecollective.com';



$ping_text = 'TESTING PING APP';



$client = new JAXL(array(
#	                   'log_path' => './jaxl.log',
	                   //'strict' => TRUE,
	                   'jid' => $user.'@'.$host,
	                   'pass' => $pass,
	                   'log_level' => JAXL_DEBUG,
			     'priv_dir' => './jlog',
			     'force_tls' => true,
                           //'auth_type' => 'SCRAM_SHA_1'
                   ));


$client->add_cb('on_auth_success', function() use ($client)
{
	_info("got on_auth_success cb, jid ".$client->full_jid->to_string());
});

$client->add_cb('on_auth_failure', function($reason) use ($client)
{
	$client->send_end_stream();
	_info("got on_auth_failure cb with reason: $reason");

});

$client->add_cb('on_disconnect', function() use ($client)
{
	_info("got on_disconnect cb");
});

$client->start();
echo "Done";
