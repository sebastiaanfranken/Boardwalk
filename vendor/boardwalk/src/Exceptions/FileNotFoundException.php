<?php

/**
 * A custom exception to handle missing files, based on a default SPL exception
 *
 * @package Boardwalk\Exceptions;
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Exceptions;

use Boardwalk\Exceptions\Exception;

class FileNotFoundException extends Exception
{
	public function __construct($file)
	{
		parent::__construct('The file <em>%s</em> could not be found.', $file);
	}
}
