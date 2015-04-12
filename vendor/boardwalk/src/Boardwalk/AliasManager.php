<?php
namespace Boardwalk;

use Exception;

class AliasManager
{
	public function __construct()
	{
		if(file_exists(root() . 'aliases.php'))
		{
			$aliases = require(root() . 'aliases.php');

			foreach($aliases as $alias => $class)
			{
				define($alias, new $class());
			}
		}
		else
		{
			throw new Exception('The file <em>' . root() . 'aliases.php</em> could not be found');
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
