<?php
namespace Boardwalk;

use Exception;

class Bootstrapper
{
	protected function __construct(){}
	protected function __clone(){}

	public static function checkServerRequirements()
	{

		/*
		 * Check the servers PHP version, it needs to be 5.4.0 or higher
		 */
		if(version_compare(phpversion(), '5.4.0', '<='))
		{
			throw new Exception('This server is running PHP version ' . phpversion() . ' but we need 5.3.0 or higher');
		}

		/**
		 * Check if we can use mysqli, which is required
		 */
		if(!extension_loaded('mysqli'))
		{
			throw new Exception('The mysqli extension is required.');
		}
	}
}