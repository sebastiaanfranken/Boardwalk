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

	/**
	 * The main constructor. Since this is a static function this is private and doesn't do much
	 * @access private
	 */
	private function __construct(){}

	/**
	 * The same goes for the magic clone method
	 * @access private
	 */
	private function __clone(){}

	/**
	 * This converts $input into an object
	 *
	 * @param mixed $input The input to convert to object
	 * @return object
	 */
	public static function toObject($input)
	{

		/*
		 * If $input is an array we'll run foreach over it and call this function
		 * recursively to parse all items in it, and convert them to an object.
		 */
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

	/**
	 * This converts $input to an array
	 *
	 * @param mixed $input The input to convert to array
	 * @return array
	 */
	public static function toArray($input)
	{
		return (array)$input;
	}

	/**
	 * This converts $input to a JSON string
	 *
	 * @param mixed $input The input to convert to a string
	 * @return string
	 */
	public static function toJSON($input)
	{
		return json_encode($input);
	}
}
