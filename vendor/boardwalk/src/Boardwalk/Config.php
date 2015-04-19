<?php

/**
 * The main system config interface
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;
use Boardwalk\Exceptions\FileNotFoundException;

class Config
{
	protected function __construct(){}
	protected function __clone(){}

	/**
	 * Bootstraps the config items. It reads a file and parses it, setting all the variables as it goes
	 *
	 * @static
	 * @return void
	 * @throws Boardwalk\Exceptions\FileNotFoundException if the application.php file cannot be found in the config directory
	 */
	public static function bootstrap()
	{
		if(file_exists(config() . 'application.php'))
		{
			$items = require config() . 'application.php';

			if(is_array($items))
			{
				foreach($items as $configKey => $configValue)
				{
					self::set($configKey, $configValue);
				}
			}
		}
		else
		{
			throw new FileNotFoundException(config() . 'application.php');
		}
	}

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