<?php

require_once 'vendor/autoload.php';


$user = '';
$pass = '';
$host = '';
$ping_text = '';


$client = new JAXL(array(
	                   'jid' => $user.'@'.$host,
	                   'pass' => $pass,
	                   'log_level' => JAXL_INFO
                   ));

$client->add_cb('on_auth_success', function() use ($client, $ping_text, $host) {
	$client->set_status('Available');
	$client->send_chat_msg($host.'/announce/online', $ping_text);
	$client->send_end_stream();
});

$client->start();