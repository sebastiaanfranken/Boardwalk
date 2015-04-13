<?php

/**
 * The main router for the entire system
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;
use Boardwalk\Exceptions\FileNotFoundException;

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

		/*
		 * Check if the routes file exists and load it. If it doesn't throw a new exception
		 */
		if(file_exists(config() . 'routes.php'))
		{
			$routes = include config() . 'routes.php';
		}
		else
		{
			throw new FileNotFoundException(config() . 'routes.php');
		}

		/*
		 * Check if it's a valid and legitimate request
		 */
		if(isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['REQUEST_URI']))
		{
			$this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
			$this->requestUri = $_SERVER['REQUEST_URI'];
			$parts = explode('/', $this->requestUri);
			$parts = array_values(array_filter($parts));

			if(count($parts) > 0)
			{
				$method = isset($parts[1]) ? $parts[1] : 'index';
				$controller = $routes[$parts[0]][$method][$this->requestMethod][0];

				$this->controller = $this->controllerPrefix . $controller;
				$this->routeController = strtolower($controller);

				$this->method = $this->requestMethod . ucfirst($method);
				$this->routeMethod = strtolower(str_replace($this->requestMethod, '', $this->method));

				$this->arguments = (count($parts) == 3) ? $parts[3] : array();
			}
			else
			{
				$controller = $routes['index'][$this->requestMethod][0];
				$method = $routes['index'][$this->requestMethod][1];

				$this->controller = $this->controllerPrefix . $controller;
				$this->routeController = strtolower($controller);

				$this->method = $method;
				$this->routeMethod = strtolower(str_replace($this->requestMethod, '', $this->method));

				$this->arguments = array();
			}

			if(method_exists($this->controller, $this->method))
			{
				$instance = new $this->controller;

				/*
				 * Check if the instance has a before method
				 */
				if(method_exists($instance, 'before'))
				{
					$instance->before();
				}

				$fn = call_user_func_array(array($instance, $this->method), $this->arguments);

				/*
				 * Check if the instance has an after method
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
					throw new Exception('The method <em>' . $this->controller . '::' . $this->method . '</em> does not return a string');
				}
			}
			else
			{
				throw new Exception('The method <em>' . $this->method . '</em> does not exist in <em>' . $this->controller . '</em>');
			}
		}
		else
		{
			throw new Exception('Unclear request');
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
