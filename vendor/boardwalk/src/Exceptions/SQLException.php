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
	private $other;

	public function __construct(mysqli $connection, $query = null)
	{
		if(!is_null($query))
		{
			$this->other = $query;
		}

		parent::__construct($connection->error, $connection->errno);
	}

	public function getOther()
	{
		return $this->other;
	}
}