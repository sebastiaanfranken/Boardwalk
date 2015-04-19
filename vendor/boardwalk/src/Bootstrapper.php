<?php
namespace Boardwalk;

use Exception;

class Bootstrapper
{
	protected static $requiredExtensions = array('mysqli', 'json');

	protected function __construct(){}
	protected function __clone(){}

	public static function checkServerRequirements()
	{

		/*
		 * Check if we're running in a virtualhost / in the root folder.
		 * The system won't work in a userdir (wontfix)
		 */
		if(strstr($_SERVER['REQUEST_URI'], '~'))
		{
			throw new Exception('Boardwalk cannot run in a userdir (running in ' . $_SERVER['REQUEST_URI'] . ')');
		}

		/*
		 * Check the servers PHP version, it needs to be 5.4.0 or higher
		 */
		if(version_compare(phpversion(), '5.4.0', '<='))
		{
			throw new Exception('This server is running PHP version ' . phpversion() . ' but we need 5.4.0 or higher');
		}
		
		/*
		 * Check if we have all the required extensions loaded into PHP
		 */
		if(count(self::$requiredExtensions))
		{
			foreach(self::$requiredExtensions as $extension)
			{
				if(!extension_loaded($extension))
				{
					throw new Exception('The ' . $extension . ' extension is required, but not loaded. Aborting.');
				}
			}
		}
	}
}
