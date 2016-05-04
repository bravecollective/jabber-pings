<?php

use Carbon\Carbon;

class ApiController extends BaseController
{
    const LAYOUT = 'layouts.home';

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |   Route::get('/', 'HomeController@showWelcome');
    |
    */


	public function apiSendMucMessage() {

		$api_key = Input::get('api_key');
		$api_secret = Input::get('api_secret');
		$room = Input::get('room');
		$body = Input::get('body');
		$ping = Input::get('ping');
		$signature = Input::get('signature', false);

		$users = RestApiUser::where('key_id' , '=', $api_key)
			->where('key_secret' , '=', $api_secret)
			->where('active' , '=', 1)
			->take(1)->get();

		if (isset($users[0])) {
			$user = $users[0];
			$log = MucLog::create([
				'rest_api_user_id' => $user->id,
				'body' => $body,
				'muc' => $room,
				'ping' => $ping,
			]);
			$log->save();
			$this->_sendMUCMessage($log, $user);
			
			return Response::json(['success' => true]);
		} else {
			return Response::view('errors.403', array(), 403);
		}
	}

    private function _sendMUCMessage(MucLog $log, RestApiUser $appUser)
    {
        // Config Details
        $host = Config::get('jabber.server');
        $user = Config::get('jabber.user');
        $pass = Config::get('jabber.password');

        // Create Client
        $client = new \JAXL(array(
            'log_path' => '/www/ping.braveineve.com/jaxlMuc.log',
            'jid' => $user.'@'.$host,
            'pass' => $pass,
            'log_level' => JAXL_INFO,
            'force_tls' => true,
            'stream_context' => stream_context_create(['ssl' => ['verify_peer' => false]], ['verify_peer' => false])
        ));

	    $client->require_xep(array(
		    '0045',	// MUC
	    ));

	    // Add Callbacks
	    $client->add_cb('on_auth_success', function() use ($client, $log, $appUser)
	    {
		    // part one
		    $nick = 'pingbot';
		    $body = "[{$appUser->app_name}]: {$log->body}";

		    $rooms = explode(',', $log->muc);
		    foreach ($rooms as $room)
		    {
			    $roomJid = trim($room).'@conference.bravecollective.com';
			    $room_jid = $roomJid.'/'.$nick;

			    $client->xeps['0045']->join_room($room_jid);
			    $client->xeps['0045']->send_groupchat($roomJid, $body);

			    // Send all users in the room a ping
			    if ($log->ping) {
				    $client->xeps['0045']->send_groupchat($roomJid, '!ping');
			    }
			    $client->xeps['0045']->leave_room($room_jid);
		    }

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

        // Startup Client
        $client->start();
    }
}
