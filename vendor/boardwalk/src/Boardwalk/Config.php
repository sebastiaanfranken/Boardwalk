<?php

/**
 * The main system config interface
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;

class Config
{
	protected function __construct(){}
	protected function __clone(){}

	/**
	 * Gets a key, if it's set.
	 *
	 * @param string $key The key to check if it's set
	 * @static
	 * @return mixed
	 */
	public static function get($key)
	{
		return defined('APP_' . strtoupper($key)) ? constant('APP_' . strtoupper($key)) : false;
	}

	/**
	 * Sets a key with a value, if it's not set
	 *
	 * @param string $key The key to name it.
	 * @param string $value The value to set
	 * @static
	 * @return void
	 * @throws Exception if the value is already set (cannot redeclare a constant)
	 */
	public static function set($key, $value)
	{
		if(defined('APP_' . strtoupper($key)))
		{
			$message = 'Cannot (re)set the value of <em>%s</em> (%s) after it has been set.';
			throw new Exception(sprintf($message, $key, 'APP_' . strtoupper($key)));
		}
		else
		{
			define('APP_' . strtoupper($key), $value);
		}
	}
}