<?php

/**
 * This is a HTML element utility
 *
 * @package Boardwalk\Utilities
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk\Utilities;

use InvalidArgumentException;
use DOMDocument;
use DOMAttr;
use DOMNode;

abstract class HTMLElement
{

	/**
	 * @var DOMDOcument $dom Stores the DOMDocument instance
	 * @access protected
	 */
	protected $dom;

	/**
	 * @var DOMNode $element Stores the current node
	 * @access protected
	 */
	protected $element;
	
	/*
	 * Builds a new DOMDocument and appends a HTML tag to it
	 */
	public function __construct()
	{
		$element = strtolower(get_called_class());
		
		$this->dom = new DOMDocument('1.0', 'utf-8');
		$this->element = $this->dom->createElement($element);
		$this->dom->appendChild($this->element);
	}
	
	/**
	 * Sets an attribute on the tag
	 *
	 * @param string $key The key to set
	 * @param string $value The value to set
	 * @return Boardwalk\Utilities\HTMLElement
	 */
	public function setAttribute($key, $value)
	{
		$attribute = new DOMAttr($key, $value);
		$this->element->setAttributeNode($attribute);
		return $this;
	}
	
	/**
	 * Sets one or more attributes
	 *
	 * @see setAttribute
	 * @param array $attributes The attributes
	 * @return Boardwalk\Utilities\HTMLElement
	 */
	public function setAttributes(array $attributes = array())
	{
		if(count($attributes) > 0)
		{
			foreach($attributes as $key => $value)
			{
				$this->setAttribute($key, $value);
			}
		}
		else
		{
			$message = '<em>%s</em> is expecting an array with a least one key => value pair';
			throw new InvalidArgumentException(sprintf($message, __METHOD__));
		}
		
		return $this;
	}
}
