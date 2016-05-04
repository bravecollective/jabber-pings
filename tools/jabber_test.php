<?php

require_once 'vendor/autoload.php';



$user = 'pingbot';
$pass = 'secret';
$host = 'bot.bravecollective.com';

$ping_text = 'TESTING PING APP';



$client = new JAXL(array(
	                   'log_path' => './jaxl.log',
	                   'strict' => TRUE,
'host' => 'xmpp.bravecollective.com',
'port' => '10234',
	                   'jid' => $user.'@'.$host,
	                   'pass' => $pass,
	                   'log_level' => JAXL_DEBUG
                   ));

$client->add_cb('on_auth_success', function() use ($client, $ping_text, $host) {
	$client->set_status('Available');
	$client->send_chat_msg($host.'/announce/fc', $ping_text);
	$client->send_end_stream();
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
