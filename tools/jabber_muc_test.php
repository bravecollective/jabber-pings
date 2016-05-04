<?php

require_once 'vendor/autoload.php';

$user = 'pingbot';
$pass = 'secret';
$host = 'bot.bravecollective.com';

$ping_text = 'TESTING PING APP';

$client = new \JAXL(array(
	'log_path' => './jaxl.log',
	'jid' => $user.'@'.$host,
	'pass' => $pass,
	'log_level' => JAXL_DEBUG,
	'force_tls' => true,
));

$client->require_xep(array(
	'0045',	// MUC
));

var_dump($client);
var_dump($client->xeps['0045']);

$body = 'This is an automated test message. Thank you.';
$roomJid = 'it@conference.bravecollective.com';
$nick = 'pingbot';
$room_jid = $roomJid.'/'.$nick;

// Add Callbacks
$client->add_cb('on_auth_success', function() use ($client, $body, $room_jid, $roomJid)
{
	// part one
	$client->send_chat_msg('bravecollective.com/announce/admin', $body.' - Start');

	$client->xeps['0045']->join_room($room_jid);
	$client->xeps['0045']->send_groupchat($roomJid, $body);
	$client->xeps['0045']->leave_room($room_jid);

	$client->send_chat_msg('bravecollective.com/announce/admin', $body.' - End');

	$client->send_end_stream();
});

/*
$client->add_cb('on_presence_stanza', function($stanza) use ($client, $body, $room_jid){
	global $client, $room_full_jid;

	$from = new XMPPJid($stanza->from);

	// self-stanza received, we now have complete room roster
	if(strtolower($from->to_string()) == strtolower($room_full_jid->to_string())) {
		if(($x = $stanza->exists('x', NS_MUC.'#user')) !== false) {
			if(($status = $x->exists('status', null, array('code'=>'110'))) !== false) {
				$item = $x->exists('item');
				_info("xmlns #user exists with x ".$x->ns." status ".$status->attrs['code'].", affiliation:".$item->attrs['affiliation'].", role:".$item->attrs['role']);

				$client->xeps['0045']->send_groupchat($room_jid, $body);
				$client->xeps['0045']->leave_room($room_jid);

				$client->send_chat_msg('bravecollective.com/announce/admin', $body.' - End');

				$client->send_end_stream();
			}
			else {
				_info("xmlns #user have no x child element");
			}
		}
		else {
			_warning("=======> odd case 1");
		}
	}

});
*/

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
