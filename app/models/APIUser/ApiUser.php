<?php

use Illuminate\Auth\UserInterface;

class ApiUser extends Eloquent implements UserInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'api_users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('token');

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $fillable = array('id', 'token', 'remember_token', 'character_name', 'alliance_id', 'alliance_name', 'tags', 'status', 'permission');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->token;
	}

	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	public static function allCanSend($user)
	{
		$groups = [];
		$namespace = Config::get('braveapi.application-permission-namespace');
		$perms = json_decode($user->user_permissions);

		if($perms === null)
		{
			return [];
		}

		foreach($perms as $perm)
		{
			if(substr($perm, 0, strlen($namespace.'send.')) == $namespace.'send.')
			{
				$slug = substr($perm, strlen($namespace.'send.'));
				$groups[$slug] = self::_mapGroupSlugToName($slug);
			}
		}

		return $groups;
	}

	public static function allCanReceive($user)
	{
		$groups = [];
		$namespace = Config::get('braveapi.application-permission-namespace');
		$perms = json_decode($user->user_permissions);

		if($perms === null)
		{
			return [];
		}

		foreach($perms as $perm)
		{
			if(substr($perm, 0, strlen($namespace.'receive.')) == $namespace.'receive.')
			{
				$slug = substr($perm, strlen($namespace.'receive.'));
				$groups[$slug] = self::_mapGroupSlugToName($slug);
			}
		}

		// hardcoded hell, TODO: remove
		if(empty($groups))
		{
			$groups_map = Config::get('ping-group-map');
			$groups['hero'] = _mapGroupSlugToName('hero');
		}

		return $groups;
	}

	private static function _mapGroupSlugToName($slug)
	{
		$names = Config::get('braveapi.ping-group-map');
		if(isset($names[$slug]))
		{
			return $names[$slug];
		}
		return $slug;
	}

}