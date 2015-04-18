<?php

/**
 * An extension exception if/when a method in a class isn't implemented anymore/yet
 *
 * @package Boardwalk\Exceptions
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Exceptions;

use Exception;

class NotImplementedException extends Exception
{

	/**
	 * Throws the main exception
	 *
	 * @param string $method The method to pass on to the parent's constructor
	 * @return Exception
	 */
	public function __construct($method)
	{
		$message = '<em>%s</em> is not implemented.';
		$error = sprintf($message, $method);

		parent::__construct($error);
	}
}