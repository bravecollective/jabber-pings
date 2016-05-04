<?php

use Illuminate\Auth\GenericUser;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserProviderInterface;

class ApiUserProvider implements UserProviderInterface {

	public function retrieveById($identifier)
	{
		return ApiUser::find($identifier);
	}

	public function retrieveByCredentials(array $credentials)
	{
		try
		{
			$user = ApiUser::where('token', '=', $credentials['token'])->get();
			if(isset($user[0]))
			{
				return $user[0];
			}
			else
			{
				$api = new Brave\API(Config::get('braveapi.application-endpoint'), Config::get('braveapi.application-identifier'), Config::get('braveapi.local-private-key'), Config::get('braveapi.remote-public-key'));
				$result = $api->core->info(array('token' => $credentials['token']));

				if(!isset($result->character->name))
				{
					return Redirect::route('logintest')
					               ->with('flash_error', 'Login Failed, Please Try Again');
				}

				$user = $this->updateUser($credentials['token'], $result);

				return $user;
			}
		}
		catch(Exception $e)
		{
			echo "\n\n";
			echo $e->getMessage();
			echo "\n\n";
			echo "Credentials:\n";
			var_dump($credentials);
			echo "\n\n";
//			var_dump($e->getTrace());
			exit;
			return null;
		}
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{
		if(isset($user->token) and $user->token == $credentials['token'])
		{
			return true;
		}

		try
		{
			$api = new Brave\API(Config::get('braveapi.application-endpoint'), Config::get('braveapi.application-identifier'), Config::get('braveapi.local-private-key'), Config::get('braveapi.remote-public-key'));
			$result = $api->core->info(array('token' => $credentials['token']));

			if(!isset($result->character->name))
			{
				return Redirect::route('logintest')
				               ->with('flash_error', 'Login Failed, Please Try Again');
			}

			$this->updateUser($credentials['token'], $result);
			return true;
		}
		catch(Exception $e)
		{
			return Redirect::route('logintest')
			               ->with('flash_error', 'Login Failed, Please Try Again');
		}
	}

	public function retrieveByToken($identifier, $token)
	{

	}

	public function updateRememberToken(UserInterface $user, $token)
	{

	}

	private function updateUser($token, $result)
	{
		// validate permissions
		$permission = 0;
		//foreach(Config::get('braveapi.auth-edit-tags') as $tag)
		//{
		//	if(in_array($tag, $result->tags)) // check for special group
		//	{
		//		$permission = 1;
		//		break;
		//	}
		//}
		// per user overrides
		foreach(Config::get('braveapi.auth-edit-users') as $id)
		{
			if($id == $result->character->id) // check for special group
			{
				$permission = 1;
				break;
			}
		}

		// Get alliance info
		$api = new Brave\API(Config::get('braveapi.application-endpoint'), Config::get('braveapi.application-identifier'), Config::get('braveapi.local-private-key'), Config::get('braveapi.remote-public-key'));
//		$ashort = "";
//		if ($result->alliance) {
//		    $alliance_result = $api->lookup->alliance(array('search' => $result->alliance->id, 'only' => 'short'));
//		    $ashort = $alliance_result->short;
//		}

		// filter permissions and save only the relevant ones
		$relevant_perms = [];
		$perms = $result->perms;

		$namespace = Config::get('braveapi.application-permission-namespace');
		foreach($perms as $perm)
		{
			// discard 'core' root namespaces permissions
			if(substr($perm, 0, 5) != 'core.')
			{
				// echo substr($perm, 0, strlen($namespace)), ' == ', $namespace, "\n";
				if(substr($perm, 0, strlen($namespace)) == $namespace)
				{
					$relevant_perms[] = $perm;
					$key = explode('.', $perm);
                    if($key[1] == "send")
                    {
                        $permission = 1;
                    }

					$key = '';
                    // do horrible things
                    $check = strpos($perm, 'ping.receive.');
                    if ($check) {
                    	$key = substr($perm, 12);
                    }

                    $check = strpos($perm, 'ping.send.');
                    if ($check) {
                    	$key = substr($perm, 9);
                    }

					//$key = end($key);
					Group::firstOrCreate(array('key' => strtolower($key)));
				}
			}
		}
		$relevant_perms = json_encode($relevant_perms);

		// testing debug, please ignore lel
		if($result->character->id == 93647416)
		{
			$rp =json_decode($relevant_perms);
			sort($rp);
			//dd([$rp, $result]);
		}

		// check for existing user
		$userfound = ApiUser::find($result->character->id);
		if($userfound == false)
		{
			// no user found, create it
			$userfound = ApiUser::create(array(
				'id' => $result->character->id,
				'token' => $token,
				'remember_token' => '',
				'character_name' => $result->character->name,
				'alliance_id' => ($result->alliance) ? $result->alliance->id : 0,
				'alliance_name' => ($result->alliance) ? $result->alliance->name : "",
//				'alliance_ticker' => $ashort,
				'user_permissions' => $relevant_perms,
				'tags' => json_encode($result->tags),
				'status' => 1,
				'permission' => $permission
			));
		}
		else
		{
			// update the existing user
			$userfound->token = $token;
			$userfound->permission = $permission;
			$userfound->token = $token;
			$userfound->character_name = $result->character->name;
			$userfound->alliance_id = ($result->alliance) ? $result->alliance->id : 0;
			$userfound->alliance_name = ($result->alliance) ? $result->alliance->name : "";
//			$userfound->alliance_ticker = $ashort;
			$userfound->user_permissions = $relevant_perms;
			$userfound->tags = json_encode($result->tags);
			$userfound->status = 1;

			$userfound->save();
		}

		return $userfound;
	}
}
