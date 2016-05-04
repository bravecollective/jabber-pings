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
    |   Route::get('/', 'HomeController@showWelcome');
    |
    */

	private function _getPingMap() {
		$ping_map = [];
		$pings_map = Group::all();
		foreach($pings_map as $i => $group) {
			if (!empty($group->key)) 
				$ping_map[$group->key] = $group->name;
		}
		return $ping_map;
	}

    public function listAllPingsView()
    {
        $user = Auth::user();

        $allowed_groups = array_keys(ApiUser::allCanReceive($user));
		if (!empty($allowed_groups)) {
			$group_ids = Group::whereIn('key', $allowed_groups)->lists('id');

	        $pings = Ping::whereIn('group_id', $group_ids)->with('group')->orderBy('created_at', 'desc')->paginate(20);
	        $pings->setBaseUrl('history');
		} else {
			$group_ids = Group::where('key', '=', 'hero')->lists('id');
			$pings = Ping::whereIn('group_id', $group_ids)->with('group')->orderBy('created_at', 'desc')->paginate(20);
		}

		$ping_map = $this->_getPingMap();

        $pageContentView = View::make('home', array('pings' => $pings, 'paginate' => true, 'ping_map' => $ping_map));

        // make main layout page
        $layoutView = View::make(self::LAYOUT)
            ->with('page_content', $pageContentView)
            ->nest('navigation', 'navigation')
            ->nest('footer', 'parts/footer');

        return $layoutView;
    }

    public function listPingHistoryView()
    {
        $user = Auth::user();

        $allowed_groups = array_keys(ApiUser::allCanReceive($user));
        $group_ids = Group::whereIn('key', $allowed_groups)->lists('id');

        $pings = Ping::whereIn('group_id', $group_ids)->with('group')->orderBy('created_at', 'desc')->paginate(20);
        $pings->setBaseUrl('history');

		$ping_map = $this->_getPingMap();

        $pageContentView = View::make('history', array('pings' => $pings, 'paginate' => true, 'ping_map' => $ping_map));

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
        $user = Auth::user();
        $allowed_groups = array_keys(ApiUser::allCanSend($user));

        // Kill page if they cant send any groups
        if (empty($allowed_groups)) {
            return Redirect::route('home')
                ->with('flash_error', 'You dont have the ability to send pings.')
                ->withInput();
        }

        // build list of sendable groups
        $groups_list = [];
        $groups_list[''] = '';
        $groups = Group::whereIn('key', $allowed_groups)->where('status', '=', '1')->get();
        foreach ($groups as $group) {
            $groups_list[$group->id] = $group->name;
        }
        asort($groups_list);

        // make Ping history page
        $pageContentView = View::make('new', array(
            'defaultPingText' => $defaultPingText,
            'pingGroups' => $groups_list
        ));

        // Build view
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
            'pingGroup' => 'required',
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
            $group_id = Input::get('pingGroup');
            try {
                $group = Group::findOrFail($group_id);
            }
            catch (ModelNotFoundException $e) {
                return Redirect::route('add_timer')
                    ->with('flash_error', 'This ping group does not exist. Please Try Again.')
                    ->withInput();
            }

            // Tolled u group 9001%
            //$get_trolled = [];
            $disabled_groups = [];

            $get_trolled = [];
            //$disabled_groups = ['awoxalert'];

            // These groups are disabled
            if(in_array($group->key, $disabled_groups) or $group->status === 0) {
                return Redirect::route('add_timer')
                    ->with('flash_error', 'This ping group is currently disabled. Please try again later.')
                    ->withInput();
            }

            // Figure out what groups this person can send too
            $groups = ApiUser::allCanSend(Auth::user());

            // Check if they are trying to send to a group that they can't
            if(!isset($groups[$group->key]))
            {
                return Redirect::route('add_timer')
                               ->with('flash_error', 'That group does not exist or you don\'t have permission to send it messages.')
                               ->withInput();
            }

            // get ping text
            $ping_text = "\n".trim(Input::get('pingText'));

            // save ping to DB
            $ping = Ping::create(array(
                'message' => $ping_text,
                'group_id' => $group->id,
                'user_id' => Auth::user()->id,
            ));

            // send the group ping
            $this->_sendGroupPing($ping, $group->key);

            // Redirect when complete
            return Redirect::route('home')->with('flash_msg', 'Ping to "'.$group->key.'" Was Sent!');
        }
    }

    private function _sendGroupPing(Ping $ping, $group_key)
    {
        // Config Details
        $host = Config::get('jabber.server');
        $user = Config::get('jabber.user');
        $pass = Config::get('jabber.password');

        // Create Client
        $client = new JAXL(array(
            'log_path' => '/www/ping.braveineve.com/jaxl.log',
            'jid' => $user.'@'.$host,
            'pass' => $pass,
            'log_level' => JAXL_DEBUG,
            'force_tls' => true,
            'stream_context' => stream_context_create(['ssl' => ['verify_peer' => false]], ['verify_peer' => false])
        ));

        // add logging text ot the bottom of the ping
        $character_name = Auth::user()->character_name;
        $alliance_name = Auth::user()->alliance_name;
        $ping_text = "\n{$ping->message}\n\n##### SENT BY: {$character_name} ({$alliance_name}); TO: online.{$group_key}; WHEN: ".$ping->created_at." #####";

        // Add Callbacks
        $client->add_cb('on_auth_success', function() use ($host, $client, $ping_text, $group_key)
                {

            $client->send_chat_msg('bravecollective.com/announce/'.$group_key, $ping_text);
	    $this->slack($ping_text, $group_key);
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

    private function slack($message, $group) {
	if (!in_array($group, array('hero', 'fc', 'brave', 'defcon', 'command', 'fun', 'stratop', 'all', 'ccpsux', 'dojo', 'awoxalert', 'fcblock', 'social', 'casual'))) {
	    return -1;
	}
        $ch = curl_init("https://brave-collective.slack.com/services/hooks/slackbot?token=secret&channel=command");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POST, count($message));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

}

