<?php

/*
 * The main application entrance point. The application gets bootstrapped here and
 * this is where the autoloader is placed
 */

if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php'))
{
	require_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions.php');
}
else
{
	ini_set('display_errors', 'On');
	trigger_error('The main functions library (' . __DIR__ . DIRECTORY_SEPARATOR . 'functions.php) could not be found', E_USER_ERROR);
}

spl_autoload_register(function($class) {

	$parts = explode('\\', $class);

	if(count($parts) > 0)
	{
		if(strtolower($parts[0]) == 'app')
		{
			$file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		}
		else
		{
			$vendor = array_shift($parts);
			$filename = ltrim(str_replace($vendor, '', str_replace('\\', DIRECTORY_SEPARATOR, $class)), '\\');

			$file = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . strtolower($vendor) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $filename . '.php';
		}

		if(file_exists($file))
		{
			require_once($file);
		}
		else
		{
			ini_set('display_errors', 'On');
			trigger_error('The requested file <em>' . $file . '</em> was not found. Full calling class <em>' . $class . '</em>', E_USER_ERROR);
		}
	}
	else
	{
		continue;
	}
});

/*
 * Register our exception handler to enable pretty printing of exceptions
 */
Boardwalk\ExceptionHandler::register();

/*
 * Check if the server meets some requirements
 */
Boardwalk\Bootstrapper::checkServerRequirements();

/*
 * Class alias manager
 */
# $aliases = new Boardwalk\AliasManager();

/*
 * Setup all the basic constants
 */
Boardwalk\Config::bootstrap();

/*
 * Check if we're in debug mode and display errors if we are
 */
if(Boardwalk\Config::get('debug'))
{
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
}

/*
 * Our router handles URL's nicely
 */
# $router = new Boardwalk\Router();
# print $router->response();

$router = new Boardwalk\Router();
//$router->map('GET', '/', 'Index@getIndex', 'home');

if(file_exists(config() . 'routes.php'))
{
	$routes = include config() . 'routes.php';
	$router->addRoutes($routes);
}

$routeMatch = $router->match();

if(is_array($routeMatch) && count($routeMatch) > 0)
{
	print $router->handle($routeMatch);
}
else
{
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
}

