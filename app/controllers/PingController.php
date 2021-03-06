<?php

use Carbon\Carbon;

class PingController extends BaseController
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
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function listAllPingsView()
	{
		$user = Auth::user();
		if($user->id == 93647416)
		{
			//dd($user);
		}
		$allowed_groups = array_keys(ApiUser::allCanReceive($user));

		$group_ids = Group::whereIn('key', $allowed_groups)->lists('id');
		$pings = Ping::whereIn('group_id', $group_ids)->orderBy('created_at', 'desc')->paginate(20);
		$pings->setBaseUrl('history');

		// make ping page
		$pageContentView = View::make('home')
			->with(array('pings' => $pings));

		// make main layout page
		$layoutView = View::make(self::LAYOUT)
			->with('page_content', $pageContentView)
			->nest('navigation', 'navigation')
			->nest('footer', 'parts/footer');

		return $layoutView;
	}

	public function listPingHistoryView()
	{
		$pings = Ping::orderBy('created_at', 'desc')->paginate(20);

		// make Ping history page
		$pageContentView = View::make('history', array('pings' => $pings, 'paginate' => true));

		// make main layout page
		$layoutView = View::make(self::LAYOUT)
		                  ->with('page_content', $pageContentView)
		                  ->nest('navigation', 'navigation')
		                  ->nest('footer', 'parts/footer');

		return $layoutView;
	}

	public function addPingView()
	{
		$defaultPingText = Config::get('jabber.default-text');

			// make Ping history page
		$pageContentView = View::make('new', array(
			'defaultPingText' => $defaultPingText,
			'pingGroups' => ApiUser::allCanSend(Auth::user())
		));

		$this->layout = self::LAYOUT;
		$view = View::make(self::LAYOUT)
			->with('page_content', $pageContentView)
            ->nest('navigation', 'navigation')
            ->nest('footer', 'parts/footer');

		return $view;
	}

	public function addPingAction()
	{
		$rules = array(
			'pingText' => 'required',
		);

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::route('add_timer')
				->with('flash_error', 'Please fill in all Ping Information')
				->withInput();
		}
		else
		{
			//
			$groups = ApiUser::allCanSend(Auth::user());
			$slug = Input::get('pingGroup');

			//
			if(!isset($groups[$slug]))
			{
				return Redirect::route('add_timer')
				               ->with('flash_error', 'That group does not exist or you don\'t have permission to send it messages.')
				               ->withInput();
			}

			// ping details
			$ping_text = "\n".Input::get('pingText');
			$ping_group = Input::get('pingGroup', false);

			// get group ID
			$group_id = 1;
			if($ping_group !== false)
			{
				$group = Group::where('key', '=', Input::get('pingGroup'))->first();
				$group_id = $group->id;
			}

			// save ping to DB
			$ping = Ping::create(array(
				'message' => $ping_text,
				'group_id' => $group_id,
				'user_id' => Auth::user()->id,
			));

			// send the group ping
			if($ping_group !== false)
			{
				$this->_sendGroupPing($ping, Input::get('pingGroup'));
			}

			// requested ping to legacy server?
			if(Input::get('legacyPing', false) === '1')
			{
				$this->_sendLegacyPing($ping);
			}

			// Redirect when complete
			return Redirect::route('home')->with('flash_msg', 'Ping Was Sent!');
		}
	}

	function _sendLegacyPing(Ping $ping)
	{
		// Config Details
		$host = Config::get('jabber.legacy-server');
		$user = Config::get('jabber.legacy-user');
		$pass = Config::get('jabber.legacy-password');

		// Create Client
		$client = new JAXL(array(
			'log_path' => './legacy_jaxl.log',
			'jid' => $user.'@'.$host,
			'pass' => $pass,
			'log_level' => JAXL_ERROR
		));

		// add logging text to the bottom of the ping
		$character_name = Auth::user()->character_name;
		$alliance_name = Auth::user()->alliance_name;
		$ping_text = "\n{$ping->message}\n\n##### SENT BY: {$character_name} ({$alliance_name}); TO: online.all; WHEN: ".$ping->created_at." #####";

		// Add Callbacks
		$client->add_cb('on_auth_success', function() use ($host, $client, $ping_text) {
			$client->send_chat_msg($host.'/announce/online', $ping_text);
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

	function _sendGroupPing(Ping $ping, $group)
	{
		// Config Details
		$host = Config::get('jabber.server');
		$user = Config::get('jabber.user');
		$pass = Config::get('jabber.password');

		// Create Client
		$client = new JAXL(array(
			'log_path' => './jaxl.log',
			'jid' => $user.'@'.$host,
			'pass' => $pass,
			'log_level' => JAXL_DEBUG
		));

		// add logging text ot the bottom of the ping
		$character_name = Auth::user()->character_name;
		$alliance_name = Auth::user()->alliance_name;
		$ping_text = "\n{$ping->message}\n\n##### SENT BY: {$character_name} ({$alliance_name}); TO: online.{$group}; WHEN: ".$ping->created_at." #####";

		// Add Callbacks
		$client->add_cb('on_auth_success', function() use ($host, $client, $ping_text) {
			$client->send_chat_msg($host.'/announce/'.$group, $ping_text);
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