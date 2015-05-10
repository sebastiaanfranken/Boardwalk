<?php
namespace App\Utilities;

use Boardwalk\Utilities\HTMLElement;

class Tag extends HTMLElement
{
	public function __construct($tag)
	{
		parent::__construct(false);
		
		$this->element = $this->dom->createElement($tag);
		$this->dom->appendChild($this->element);
	}
}