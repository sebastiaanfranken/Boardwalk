<?php

/**
 * The main view class, this handles and creates views
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

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
	 * The constructor, sets the viewfile and variables
	 *
	 * @param string $viewfile The viewfile to load
	 * @param array $variables Any variables that need to be parsed in the view
	 */
	public function __construct($viewfile, array $variables = array())
	{
		$this->viewfile = app() . 'Views' . DIRECTORY_SEPARATOR . $viewfile . $this->suffix;
		$this->variables = $variables;
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
				$$name = $value;
			}
		}
		
		require $this->viewfile;
		
		return ob_get_clean();
	}
}