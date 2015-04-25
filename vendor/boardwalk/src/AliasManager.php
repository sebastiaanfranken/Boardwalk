<?php

/**
 * This manages aliases in the system. An alias is simply a short for a fully qualified class name, shortened with define()
 *
 * @package Boardwalk;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;

class AliasManager
{

	/**
	 * The constructor, checks if the aliases.php fle in the config folder exists and parse it if it does
	 */
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
	}

	/**
	 * Add a new alias
	 *
	 * @param string $alias The alias to use
	 * @param mixed $class The FQDN to use for the class name
	 * @return AliasManager
	 */
	public function addAlias($alias, $class)
	{
		if(!defined($alias))
		{
			define($alias, new $class());
		}
		else
		{
			throw new Exception(sprintf('The alias <em>%s</em> is already defined.', $alias));
		}

		return $this;
	}
}
