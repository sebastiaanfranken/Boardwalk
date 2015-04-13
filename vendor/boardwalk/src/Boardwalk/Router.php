<?php

/**
 * The main router for the entire system
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;

class Router
{
	/**
	 * @var string The request method
	 * @access protected
	 */
	protected $requestMethod;

	/**
	 * @var string The request URI
	 * @access protected
	 */
	protected $requestUri;

	/**
	 * @var string The controller (FQDN)
	 * @access protected
	 */
	protected $controller;

	/**
	 * @var string The controller prefix for the FQDN controller name
	 * @access protected
	 */
	protected $controllerPrefix = 'App\\Controllers\\';

	/**
	 * @var string The method
	 * @access protected
	 */
	protected $method;

	/**
	 * @var array The arguments passed to the method
	 * @access protected
	 */
	protected $arguments;

	/**
	 * @var string The "local" controller name, without any prefixes or other edits
	 * @access protected
	 */
	protected $routeController;

	/**
	 * @var string The "local" method, without any edits
	 * @access protected
	 */
	protected $routeMethod;

	/**
	 * @var string The output from the controller instance (called in the constructor)
	 * @access protected
	 */
	protected $output;

	/**
	 * The constructor, loads the routes.php file and parses it.
	 * Also parses the request and loads the correct method based
	 * on the rules in routes.php
	 *
	 * @return void
	 * @throws Exception If the method doesn't exist in the controller
	 * @throws Exception If the _REQUEST_METHOD and _REQUEST_URI variables aren't set
	 */
	public function __construct()
	{
		if(file_exists(config() . 'routes.php'))
		{
			$routes = include config() . 'routes.php';
		}
		else
		{
			throw new Exception('The file <em>' . config() . 'routes.php</em> does not exist');
		}

		#if(isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['REQUEST_URI']))
		if(isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['QUERY_STRING']))
		{
			$this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
			//$this->requestUri = $_SERVER['REQUEST_URI'];
			$this->requestUri = $_SERVER['QUERY_STRING'];
			$parts = explode('/', $this->requestUri);
			$parts = array_values(array_filter($parts));

			if(!isset($parts[0]))
			{
				$this->controller = $this->controllerPrefix . 'Index';
				$this->routeController = 'index';
			}
			else
			{
				$this->controller = $this->controllerPrefix . ucfirst($parts[0]);
				$this->routeController = $parts[0];
				array_shift($parts);
			}

			if(!isset($parts[0]))
			{
				$this->method = 'getIndex';
				$this->routeMethod = 'index';
			}
			else
			{
				$this->method = strtolower($this->requestMethod) . ucfirst($parts[0]);
				$this->routeMethod = $parts[0];
				array_shift($parts);
			}

			$this->arguments = (count($parts) > 0) ? $parts : array();

			if($this->routeController == 'index' && $this->routeMethod == 'index')
			{
				$method = $routes[$this->routeController][$this->requestMethod];
			}
			else
			{
				$method = $routes[$this->routeController][$this->routeMethod][$this->requestMethod];
			}

			if(method_exists($this->controller, $method))
			{
				$instance = new $this->controller;	

				/*
				 * If the controller has a before method call it before the main function
				 */
				if(method_exists($instance, 'before'))
				{
					$instance->before();
				}

				$fn = call_user_func_array(
					array($instance, $method),
					$this->arguments
				);

				/**
				 * If the controller has an after method call if after now
				 */
				if(method_exists($instance, 'after'))
				{
					$instance->after();
				}

				if(is_string($fn))
				{
					$this->output = $fn;
				}
				else
				{
					throw new Exception('The method <em>' . $this->controller . '::' . $method . '</em> does not return anything');
				}
			}
			else
			{
				throw new Exception('The method <em>' . $method . '</em> does not exist in <em>' . $this->controller . '</em>');
			}
		}
		else
		{
			throw new Exception('Not a clear request');
		}
	}

	/**
	 * Returns the output of this class, which is the response a route gives
	 *
	 * @return string
	 */
	public function response()
	{
		return $this->output;
	}

	/**
	 * When the class is printed as a string call the respone function to output
	 * something sane
	 *
	 * @return string
	 * @see response()
	 */
	public function __toString()
	{
		return $this->response();
	}
}
