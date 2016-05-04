<?php

/**
 * Ping Group Model
 */
class MucLog extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'muc_logs';

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
		'rest_api_user_id',
		'body',
		'muc',
		'ping',
	);

	public function restApiUser()
	{
		return $this->hasOne('RestApiUser', 'id', 'rest_api_user_id');
	}

}