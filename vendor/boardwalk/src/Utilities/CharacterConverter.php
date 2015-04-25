<?php
namespace Boardwalk\Utilities;

use InvalidArgumentException;

class CharacterConverter
{
	protected $rules = array();
	protected $content;

	public function __construct()
	{
		$this->rules = [];
		$this->content = null;
	}

	public function addRule($a, $b)
	{
		$this->rules[] = ['old' => $a, 'new' => $b];
		return $this;
	}

	public function addRules(array $rules)
	{
		foreach($rules as $rule)
		{
			$this->addRule($rule['old'], $rule['new']);
		}

		return $this;
	}

	public function setContent($content)
	{
		$this->content = (string)$content;
		return $this;
	}

	public function convert()
	{
		if(count($this->rules) > 0)
		{
			$content = $this->content;

			foreach($this->rules as $rule)
			{
				$content = str_replace($rule['old'], $rule['new'], $content);
			}

			return $content;
		}
		else
		{
			throw new InvalidArgumentException(sprintf('<em>%s</em> expects at least one rule, %s given', __METHOD__, count($this->rules)));
		}
	}
}