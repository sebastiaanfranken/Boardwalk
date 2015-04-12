<?php
namespace Boardwalk;

use Exception;

class AliasManager
{
	public function __construct()
	{
		if(file_exists(config() . 'aliases.php'))
		{
			$aliases = require(config() . 'aliases.php');

			foreach($aliases as $alias => $class)
			{
				define($alias, new $class());
			}
		}
		else
		{
			throw new Exception('The file <em>' . config() . 'aliases.php</em> could not be found');
		}
	}

	public function addAlias($alias, $class)
	{
		if(!defined($alias))
		{
			define($alias, new $class());
		}
		else
		{
			throw new Exception('The alias <em>' . $alias . '</em> is already defined.');
		}

		return $this;
	}
}
