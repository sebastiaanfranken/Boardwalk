<?php

/**
 * A custom exception to handle wrong data types
 *
 * @package Boardwalk\Exceptions;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl
 */

namespace Boardwalk\Exceptions;

use Exception;

class TypeException extends Exception
{
	public function __construct($method, $datatype, $var, Exception $previous = null)
	{
		$message = '<em>%s</em> expects a <em>%s</em> but was given a <em>%s</em>.';
		$error = sprintf($message, $method, $datatype, gettype($var));

		if(is_null($previous))
		{
			parent::__construct($error);
		}
		else
		{
			parent::__construct($error, 0, $previous);
		}
	}
}