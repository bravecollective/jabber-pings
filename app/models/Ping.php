<?php

/**
 * Class Timers
 *
 * Timer data model for recording individual timers for EVE online
 */
class Ping extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'pings';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * Hardcoded GroupID that relates to the EVE Static Data Dump
	 *
	 * @var int
	 */
	public static $POCOGroupID = 7;

	/**
	 * The attributes that can be edited in models.
	 *
	 * @var array
	 */
	protected $fillable = array(
		'user_id',
		'message'
	);

}