<?php

/**
 * The main view class, this handles and creates views
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;
use Boardwalk\Exceptions\FileNotFoundException;

class View
{
	/**
	 * @var string $viewfile The viewfile, without a file suffix (e.g. ".php")
	 * @access protected
	 */
	protected $viewfile;

	/**
	 * @var array $variables The variables passed to the view in the constructor
	 * @access protected
	 */
	protected $variables = array();

	/**
	 * @var string $suffix The file suffix
	 * @access protected
	 */
	protected $suffix = '.php';

	/**
	 * @var array $validSuffixes A list of valid suffixes
	 * @access protected
	 */
	protected $validSuffixes = array('.php', '.html');

	/**
	 * The constructor, sets the viewfile and variables
	 *
	 * @param string $viewfile The viewfile to load
	 * @param array $variables Any variables that need to be parsed in the view
	 */
	public function __construct($viewfile, array $variables = array())
	{
		$this->viewfile = app() . 'Views' . DIRECTORY_SEPARATOR . $viewfile . $this->suffix;
		$this->variables = $variables;

		if(!file_exists($this->viewfile))
		{
			throw new FileNotFoundException($this->viewfile);
		}
	}

	/**
	 * Makes the actual view into something usefull.
	 *
	 * @return string
	 */
	public function make()
	{
		ob_start();

		if(count($this->variables) > 0)
		{
			foreach($this->variables as $name => $value)
			{
				if($value instanceof View)
				{
					$value = $value->make();
				}
			
				$$name = $value;
			}
		}
		
		require $this->viewfile;
		
		return ob_get_clean();
	}

	/**
	 * Getter for the viewfile
	 *
	 * @return string
	 */
	public function getViewFile()
	{
		return $this->viewfile;
	}

	/**
	 * Setter for the viewfile
	 *
	 * @param string $viewfile The new viewfile
	 * @return View
	 */
	public function setViewFile($viewfile)
	{
		$this->viewfile = app() . 'Views' . DIRECTORY_SEPARATOR . $viewfile . $this->getSuffix();
		return $this;
	}

	/**
	 * Getter for the variables
	 *
	 * @return array
	 */
	public function getVariables()
	{
		return $this->variables();
	}

	/**
	 * Setter for the variables
	 *
	 * @param array $vars The variables to set
	 * @return View
	 */
	public function setVariables(array $vars = array())
	{
		$this->variables = $vars;
		return $this;
	}

	/**
	 * Getter for the suffix
	 *
	 * @return string
	 */
	public function getSuffix()
	{
		return in_array($this->suffix, $this->validSuffixes) ? $this->suffix : '.php';
	}

	/**
	 * Setter for the suffix
	 *
	 * @param string $suffix The new suffix
	 * @return View
	 */
	public function setSuffix($suffix)
	{
		if(in_array($suffix, $this->validSuffixes))
		{
			$this->suffix = $suffix;
		}

		return $this;
	}

	/**
	 * Getter for valid suffixes
	 *
	 * @return array
	 */
	public function getValidSuffixes()
	{
		return $this->getValidSuffixes;
	}

	/**
	 * Setter for valid suffixes
	 *
	 * @param array $suffixes The new suffixes to set
	 * @return View
	 */
	public function setValidSuffixes(array $suffixes = array())
	{
		$this->validSuffixes = $suffixes;
		return $this;
	}

	/**
	 * Add a single new suffix
	 *
	 * @param string $suffix The new suffix to set
	 * @return View
	 */
	public function addValidSuffix($suffix)
	{
		if(!in_array($suffix, $this->validSuffixes))
		{
			$this->validSuffixes[] = $suffix;
		}

		return $this;
	}
}
