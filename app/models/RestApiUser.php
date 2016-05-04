<?php

/**
 * Ping Group Model
 */
class RestApiUser extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rest_api_users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * The attributes that can be edited in models.
	 *
	 * @var array
	 */
	protected $fillable = array(
		'user_id',
		'app_name',
		'key_id',
		'key_secret',
		'active',
	);

	public function mucLogs()
	{
		return $this->hasMany('MucLog', 'rest_api_user_id');
	}

	public function user()
	{
		return $this->hasOne('ApiUSer', 'id', 'user_id');
	}
}