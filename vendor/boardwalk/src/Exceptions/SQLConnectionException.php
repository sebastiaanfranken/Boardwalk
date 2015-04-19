<?php

/**
 * A wrapper around the SPL exception for SQL connection errors
 *
 * @package Boardwalk\Exceptions;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Exceptions;

use Exception;
use mysqli;

class SQLConnectionException extends Exception
{
	public function __construct(mysqli $connection)
	{
		parent::__construct($connection->connect_error, $connection->connect_errno);
	}
}