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
	return root() . 'app' . DIRECTORY_SEPARATOR;
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