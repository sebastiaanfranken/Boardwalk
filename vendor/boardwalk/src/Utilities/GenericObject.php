<?php
namespace Boardwalk\Utilities;

use stdClass;
use Countable;

class GenericObject extends stdClass implements Countable
{
	protected $attributes;
	protected $attributesCounter = 0;

	public function __construct()
	{
		$this->attributes = new stdClass;
	}

	public function __set($key, $value)
	{
		$this->attributes->$key = $value;
		$this->attributesCounter++;
		return $this;
	}

	public function count()
	{
		return $this->attributesCounter;
	}

	public function __get($key)
	{
		if(property_exists($this->attributes, $key))
		{
			return $this->attributes->$key;
		}

		return $this->attributes;
	}

	public function attributes()
	{
		return $this->attributes;
	}
}