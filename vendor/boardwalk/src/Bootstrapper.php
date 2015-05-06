<?php

/**
 * The main bootstrapper for the system. Checks if the server meets the requirements
 * like certain PHP modules (mysqli, json, xml and date) and if the system isn't
 * running in a userdir, has PHP 5.4.0 or higher and if the setting "date.timezone" is
 * set in php.ini
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;

class Bootstrapper
{
	protected static $config = array();

	protected function __construct(){}
	protected function __clone(){}

	/**
	 * Checks if the server has all required extensions loaded/installed
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	private static function checkRequiredExtensions()
	{
		$requiredExtensions = self::$config['requiredExtensions'];

		if(is_array($requiredExtensions) && count($requiredExtensions) > 0)
		{
			foreach($requiredExtensions as $ext)
			{
				if(!extension_loaded($ext))
				{
					throw new Exception(sprintf('The <em>%s</em> extension is required, but not loaded or installed. Aborting.', $ext));
				}
			}
		}
	}

	/**
	 * Adds an extension to the list of required extensions
	 *
	 * @param string $name The name of the extension
	 * @param bool $recheck Recheck if an extension is added?
	 * @static
	 * @return void
	 */
	public static function addRequiredExtension($name, $recheck = true)
	{
		self::$config['requiredExtensions'][] = $name;

		if($recheck)
		{
			self::checkRequiredExtensions();
		}
	}

	/**
	 * Adds multiple new extensions to the list
	 *
	 * @param array $extensions The list of extensions
	 * @param bool $recheck Recheck if an extension is added?
	 * @static
	 * @return void
	 */
	public static function addRequiredExtensions(array $extensions, $recheck = true)
	{
		foreach($extensions as $extension)
		{
			self::addRequiredExtension($extension, $recheck);
		}
	}

	/**
	 * Checks all server requirements
	 *
	 * @static
	 * @return void
	 */
	public static function checkServerRequirements()
	{

		self::$config = require config() . 'bootstrapper.php';

		/*
		 * Check if we're running in a virtualhost / in the root folder.
		 * The system won't work in a userdir (wontfix)
		 */
		$requestUri = ltrim(trim($_SERVER['REQUEST_URI']), '/');

		if(substr($requestUri, 0, 1) === '~')
		{
			throw new Exception(sprintf('Boardwalk cannot run in a userdir (running in <em>%s</em>)', $_SERVER['REQUEST_URI']));
		}

		/*
		 * Check the servers PHP version, it needs to be 5.4.0 or higher
		 */
		//if(version_compare(phpversion(), '5.4.0', '<='))
		if(version_compare(phpversion(), self::$config['phpversion'], '<='))
		{
			throw new Exception(sprintf('This server is running PHP version <em>%s</em>, but we need 5.4.0 or higher.', phpversion()));
		}

		/*
		 * Check if we have all the required extensions loaded into PHP
		 */
		self::checkRequiredExtensions();

		/*
		 * Check if the things we need are set in PHP's config or through ini_set
		 */
		if(!ini_get('date.timezone'))
		{
			throw new Exception(sprintf('The config item <em>%s</em> is not set. Set this in the main PHP config file or through <em>ini_set()</em>.', 'date.timezone'));
		}
	}
}
