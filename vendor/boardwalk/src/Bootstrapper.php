<?php
namespace Boardwalk;

use Exception;

class Bootstrapper
{
	protected static $requiredExtensions = array('mysqli', 'json');

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
		if(count(self::$requiredExtensions) > 0)
		{
			foreach(self::$requiredExtensions as $ext)
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
		self::$requiredExtensions[] = $name;

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

		/*
		 * Check if we're running in a virtualhost / in the root folder.
		 * The system won't work in a userdir (wontfix)
		 */
		if(strstr($_SERVER['REQUEST_URI'], '~'))
		{
			throw new Exception(sprintf('Boardwalk cannot run in a userdir (running in <em>%s</em>)', $_SERVER['REQUEST_URI']));
		}

		/*
		 * Check the servers PHP version, it needs to be 5.4.0 or higher
		 */
		if(version_compare(phpversion(), '5.4.0', '<='))
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
