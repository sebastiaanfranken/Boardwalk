<?php

/**
 * This is a storage utility, with some extra cool features. It extends PHP's built in object and
 * implements the Countable interfaces, so count() will work.
 *
 * @package Boardwalk\Utilities
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Utilities;

use stdClass;
use Countable;

class GenericObject extends stdClass implements Countable
{

	/**
	 * @var stdClass $attributes Attributes (data) gets saved here
	 * @access protected
	 */
	protected $attributes;

	/**
	 * @var int $attributesCounter The number of items in attributes gets stored here
	 * @access protected
	 */
	protected $attributesCounter = 0;

	/**
	 * Resets the attributes to be blank
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->attributes = new stdClass;
	}

	/**
	 * Puts the $key and $value into the $attributes object, so we can use it later
	 *
	 * @param mixed $key The key 
	 * @param mixed $value The value
	 * @return Boardwalk\Utilities\GenericObject
	 */
	public function __set($key, $value)
	{
		$this->attributes->{$key} = $value;
		$this->attributesCounter++;
		return $this;
	}

	/**
	 * Gets $key from the attributes object, if it exists.
	 *
	 * @param mixed $key The key to check for
	 * @return mixed
	 */
	public function __get($key)
	{
		if(property_exists($this->attributes, $key))
		{
			return $this->attributes->{$key};
		}

		return $this->attributes;
	}

	/**
	 * This gets called when you run PHP's default count() function over an instance of this class.
	 * You can also call it like this, since it's public.
	 *
	 * Required for the Countable interface
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->attributesCounter;
	}

	/**
	 * Returns all data
	 *
	 * @return stdClass
	 */
	public function all()
	{
		return $this->attributes;
	}

	/**
	 * Gets the first row/field from the data
	 *
	 * @see all()
	 * @return mixed
	 */
	public function first()
	{
		return $this->all()->{0};
	}

	/**
	 * Gets the last row/field from the data
	 *
	 * @see all()
	 * @return mixed
	 */
	public function last()
	{
		$last = $this->attributesCounter - 1;
		return $this->all()->{$last};
	}

	/**
	 * This is used to find one (or more) results in the attributes (data) and returns those. It's the same general idea
	 * as the "find" method in the base Model class
	 *
	 * @param mixed $number The row number (or numbers if it's an array) to fetch from the $attributes
	 * @see all()
	 * @return mixed
	 */
	public function find($number)
	{
		if(is_int($number) && property_exists($this->attributes, $number))
		{
			return $this->all()->{$number};
		}
		elseif(is_array($number) && count($number) > 0)
		{
			$storage = new $this();

			foreach($number as $id)
			{
				$storage->{$id} = $this->find($id);
			}

			return $storage->all();
		}
		else
		{
			return $this->all();
		}
	}
}