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
	private function __construct(){}

	/**
	 * Secure any inputs provided, pass it through strip_tags() and addslashes()
	 * Do not forget to pass it through a _real_escape_string() function before you
	 * insert it in a database
	 *
	 * @param string $input The input to secure
	 * @return string
	 */
	public static function secure($input)
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
	public static function convert($input)
	{
		$output = htmlentities($input);
		$output = nl2br($output);

		return $output;
	}

	/**
	 * Implodes arrays with $seperator, $beforeLine and $afterLine as the glue
	 *
	 * @param array $inputs The input array to glue together again
	 * @param string $seperator The individual part seperator
	 * @param string $beforeLine Gets inserted before each item
	 * @param string $afterLine Gets inserted after each item. Should be the counterpart to $beforeLine
	 * @return string
	 * @throws Exception if the $inputs isn't an array with at least one item
	 */
	public static function arrayImplode(array $inputs, $seperator = ',', $beforeLine = '<em>', $afterLine = '</em>')
	{
		$output = '';

		if($seperator = ',')
		{
			$seperator = $seperator . ' ';
		}
		else
		{
			$seperator = ' ' . $seperator . ' ';
		}

		if(count($inputs) > 0)
		{
			foreach($inputs as $part)
			{
				$output .= $beforeLine . $part . $afterLine . $seperator;
			}

			$output = rtrim(rtrim($output), $seperator);
			return $output;
		}
		else
		{
			throw new \Exception('Empty array passed to ' . __FUNCTION__);
		}
	}
}
