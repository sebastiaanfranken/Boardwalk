<?php

/**
 * The utility class that handles typecasting
 *
 * @package Boardwalk\Utilities
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Utilities;

use stdClass;

class ObjectConverter
{
	private function __construct(){}
	private function __clone(){}

	public static function toObject($input)
	{
		if(is_array($input))
		{
			foreach($input as $key => $value)
			{
				$input[$key] = self::toObject($value);
			}

			return (object)$input;
		}

		return $input;
	}

	public static function toArray($input)
	{
		return (array)$input;
	}
}
