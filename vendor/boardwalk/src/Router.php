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
use Boardwalk\Utilities\ObjectConverter;

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
	 * @var string The output from the controller instance (called in the constructor)
	 * @access protected
	 */
	protected $output;
	
	/**
	 * @var array The supported request methods.
	 * @access protected
	 */
	protected $supportedRequestMethods = array('GET', 'POST');

	/**
	 * The constructor, loads the routes.php file and parses it.
	 * Also parses the request and loads the correct method based
	 * on the rules in routes.php
	 *
	 * @return void
	 * @throws Boardwalk\Exceptions\FileNotFoundException If the routes config file doesn't exist
	 * @throws Exception If the method doesn't exist in the controller
	 * @throws Exception If the _REQUEST_METHOD and _REQUEST_URI variables aren't set
	 * @throws Exception If the REQUEST_METHOD isn't supported (See supportedRequestMethods for that)
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
		 * Check if the $supportedRequestMethods is an array and if it's set
		 */
		if(!is_array($this->supportedRequestMethods) || count($this->supportedRequestMethods) == 0)
		{
			throw new Exception('The supportedRequestMethod attribute is not set or empty.');
		}

		/*
		 * Check if it's a valid and legitimate request
		 */
		if(isset($_SERVER['REQUEST_METHOD']) && isset($_SERVER['REQUEST_URI']))
		{
			if(in_array($_SERVER['REQUEST_METHOD'], $this->supportedRequestMethods))
			{
				$this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
			}
			else
			{
				throw new Exception(sprintf('The request method <em>%s</em> is currently not (yet) supported. The currently supported methods are %s', $_SERVER['REQUEST_METHOD'], implode(', ', $this->supportedRequestMethods)));
			}
			
			$this->requestUri = $_SERVER['REQUEST_URI'];
			$parts = explode('/', $this->requestUri);
			$parts = array_values(array_filter($parts));

			if(count($parts) > 0)
			{
				$method = $this->getMethodFromRoutes($parts);
				$controller = $this->getControllerFromRoutes($parts);

				$this->controller = $this->controllerPrefix . $controller;
				$this->method = $method;
				$this->arguments = (count($parts) == 3) ? $parts[3] : array();
			}
			else
			{
				$controller = $routes['index'][$this->requestMethod][0];
				$method = $routes['index'][$this->requestMethod][1];

				$this->controller = $this->controllerPrefix . $controller;
				$this->method = $method;
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
					throw new Exception(sprintf('The method <em>%s</em> does not return a string.', $this->controller . '::' . $this->method));
				}
			}
			else
			{
				throw new Exception(sprintf('The method <em>%s</em> does not exist in <em>%s</em>', $this->method, $this->controller));
			}
		}
		else
		{
			throw new Exception('Unclear request');
		}
	}

	/**
	 * Get the method to call from the routes.php config file
	 * based on the routes (array) given
	 *
	 * @param array $routes The section of routes to use for filtering
	 * @return string
	 * @todo Add error handling if a method doesn't exist
	 */
	public function getMethodFromRoutes(array $routes)
	{
		if(count($routes) == 1)
		{
			$routes[] = 'index';
		}

		$routingtable = require config() . 'routes.php';
		$routing = ObjectConverter::toObject($routingtable);

		/*
		 * Doing this in four steps to increase legability
		 */

		$method = $routing->{$routes[0]};
		$method = $method->{$routes[1]};
		$method = $method->{$this->requestMethod};
		$method = end($method);

		return $method;
	}

	/**
	 * Get the controller to call from the routes.php config file
	 * based on the routes (array) given.
	 *
	 * @param array $routes The section of routes to use for filtering
	 * @return string
	 * @todo Add error handling if a controller doesn't exist
	 */
	public function getControllerFromRoutes(array $routes)
	{
		if(count($routes) == 1)
		{
			$routes[] = 'index';
		}

		$routingtable = require config() . 'routes.php';

		/*
		 * Doing this in a few steps to increase legability
		 */

		$controller = $routingtable[$routes[0]];
		$controller = $controller[$routes[1]];
		$controller = $controller[$this->requestMethod];
		$controller = array_shift($controller);

		return $controller;
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
