<?php

/**
 * A 'extention' of PHP's internal exception, to pretty print stuff
 *
 * @package Boardwalk\Exceptions
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */
 
namespace Boardwalk\Exceptions;

use Exception as CoreException;

class Exception extends CoreException
{
	public function __construct()
	{
		if(func_num_args() > 0)
		{
			$args = func_get_args();
			$message = $args[0];
			array_shift($args);
			
			parent::__construct(vsprintf($message, $args));
		}
		else
		{
			throw new Exception(sprintf('<em>%s</em> expects at least two arguments, 0 given', __METHOD__));
		}
	}
}
