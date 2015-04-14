<?php

/**
 * A wrapper around the SPL exception for SQL errors
 *
 * @package Boardwalk\Exceptions;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Exceptions;

use Exception;
use mysqli;

class SQLException extends Exception
{
	public function __construct(mysqli $connection)
	{
		parent::__construct($connection->error, $connection->errno);
	}
}