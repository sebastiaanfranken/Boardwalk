<?php

/**
 * A custom exception to handle missing files, based on a default SPL exception
 *
 * @package Boardwalk\Exceptions;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
	public function __construct($file)
	{
		$message = 'The file <em>%s</em> could not be found.';
		$error = sprintf($message, $file);

		parent::__construct($error);
	}
}