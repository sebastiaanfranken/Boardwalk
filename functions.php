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
	return rtrim(rtrim($_SERVER['REQUEST_URI']), '/') . '/assets/';
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
 * @param array $parts The URL parts
 * @return string
 */
function url(array $parts)
{
	$routes = include config() . 'routes.php';

	preg_match('/[A-Z]/', lcfirst($parts[0]), $controllerMatches);
	$controllerMatches = end($controllerMatches);
	$controller = str_replace($controllerMatches, '-' . strtolower($controllerMatches), $parts[0]);
	$controller = strtolower($controller);

	$method = strtolower(str_replace(array('get', 'post'), '', $parts[1]));

	if($method == 'index')
	{
		return '/' . $controller . '/';
	}
	
	return '/' . $controller . '/' . $method . '/';
}