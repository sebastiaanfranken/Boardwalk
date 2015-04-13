<?php

/*
 * Every system, support or other important function goes here
 */

/**
 * A wrapper around PHP's print_r, wrapped in HTML pre tags
 *
 * @param mixed $input The content to run through print_r
 * @return string
 */
function pr($input)
{
	return '<pre>' . print_r($input, true) . '</pre>';
}

/**
 * Gets the root folder for the application
 *
 * @return string
 */
function root()
{
	return __DIR__ . DIRECTORY_SEPARATOR;
}

/**
 * Gets the app folder for the application
 *
 * @return string
 */
function app()
{
	return root() . 'App' . DIRECTORY_SEPARATOR;
}

/**
 * Gets the vendor folder for the application
 *
 * @return string
 */
function vendor()
{
	return root() . 'vendor' . DIRECTORY_SEPARATOR;
}

/**
 * Gets the config folder for the application
 *
 * @return string
 */
function config()
{
	return root() . 'config' . DIRECTORY_SEPARATOR;
}

/**
 * Gets the assets folder for the application
 *
 * @return string
 */
function assets()
{
	return root() . 'assets' . DIRECTORY_SEPARATOR;
}

/**
 * Gets the public assets folder for the application, from the front end side
 *
 * @return string
 */
function public_assets()
{
	return dirname($_SERVER['REQUEST_URI']) . 'assets/';
}

/**
 * Generates a PHP timestamp
 *
 * @param string $format The format. Has to be a valid DateTime format
 * @return string
 */
function timestamp($format = 'Y-m-d', $stamp = 'now')
{
	$timezone = ini_get('date.timezone');
	return (new \DateTime($stamp, new \DateTimeZone($timezone)))->format($format);
}

/**
 * Generates a link
 *
 * @param string $controller The controller
 * @param string $method The method
 * @param bool $removeTrailingSlash Removes the trailing slash
 * @return string
 */
function url($controller, $method = 'getIndex', $removeTrailingSlash = false)
{
	if($controller == 'index' && $method == 'getIndex')
	{
		return '/';
	}
	elseif($controller == 'index' && $method != 'getIndex')
	{
		throw new Exception('Impossible link detected');
	}
	else
	{
		$routes = include config() . 'routes.php';

		preg_match('/[A-Z]/', lcfirst($controller), $matches);
		$match = end($matches);
		$returnController = str_replace($match, '-' . strtolower($match), lcfirst($controller));
		$returnController = strtolower($returnController);

		$originalMethod = $method;
		$method = strtolower(str_replace(array('get', 'post'), '', $method));
		$requestMethod = str_replace($method, '', strtolower($originalMethod));

		if($method == 'index' && $requestMethod == 'get')
		{
			return '/' . $returnController . '/';
		}

		$url = '/'. $returnController . '/' . $method . '/';
		return $removeTrailingSlash ? rtrim($url, '/') : $url;
	}
}