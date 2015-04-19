<?php

/**
 * This file contains all text related helpers/utilities the system has
 *
 * @package Boardwalk\Utilities
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Utilities;

class Text
{
	public function __construct()
	{
	}

	/**
	 * Secure any inputs provided, pass it through strip_tags() and addslashes()
	 * Do not forget to pass it through a _real_escape_string() function before you
	 * insert it in a database
	 *
	 * @param string $input The input to secure
	 * @return string
	 */
	public function secure($input)
	{
		$output = strip_tags($input);
		$output = addslashes($output);

		return $output;
	}

	/**
	 * Converts any special characters in $input to be "safe for web".
	 *
	 * @param string $input The input to convert
	 * @return string
	 */
	public function convert($input)
	{
		$output = htmlentities($input);
		$output = nl2br($output);

		return $output;
	}
}