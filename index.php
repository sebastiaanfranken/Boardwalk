<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

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

	if(strtolower($parts[0]) == 'app')
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}
	else
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . strtolower($parts[0]) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}

	if(file_exists($file))
	{
		require_once($file);
	}
	else
	{
		ini_set('display_errors', 'On');
		trigger_error('The requested file <em>' . $file . '</em> was not found', E_USER_ERROR);
	}
});

/*
 * Register our exception handler to enable pretty printing of exceptions
 */
//$exceptionHandler = new SebastiaanFranken\ExceptionHandler();
$exceptionHandler = new Boardwalk\ExceptionHandler();

/*
 * Class alias manager
 */
# $aliases = new Boardwalk\AliasManager();

/*
 * Our router handles URL's nicely
 */
$router = new Boardwalk\Router();
print $router;