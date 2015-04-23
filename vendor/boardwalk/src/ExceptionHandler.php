<?php

/**
 * The main exception handler for the system
 *
 * @package Boardwalk
 * @author Sebastiaan Franken <sebastiaan@sebastiaanfranken.nl>
 */

namespace Boardwalk;

use Exception;

class ExceptionHandler
{

	/**
	 * The constructor sets PHP's internal exception handler to accept
	 * the handle function below as an exception handler, thus parsing
	 * exceptions in a neat (-ish) way
	 *
	 * @deprecated
	 */
	public function __construct()
	{
		//set_exception_handler(array($this, 'handle'));
		throw new Exception('Making a new isntance of ' . __CLASS__ . ' is deprecated.');
	}

	/**
	 * Sets PHP's internal exception handler to accept the handleStatic
	 * method below as an exception handler, thus parsing exception in
	 * a neat (-ish) way.
	 */
	public static function register()
	{
		set_exception_handler('Boardwalk\ExceptionHandler::handleStatic');
	}

	/**
	 * Loads a pretty HTML file and loads it with the exception data passed
	 * to it in.
	 *
	 * @param Exception The exception to parse
	 * @return void
	 */
	public function handle(Exception $exception)
	{
		$templatefile = __DIR__ . DIRECTORY_SEPARATOR . 'exception-template.php';

		if(file_exists($templatefile))
		{
			$message = $exception->getMessage();
			$code = $exception->getCode();
			$file = $exception->getTrace()[0]['file'];
			$line = $exception->getTrace()[0]['line'];
			$trace = $exception->getTraceAsString();
			$previous = $exception->getPrevious();
			$exceptionType = get_class($exception);

			require_once($templatefile);
		}
	}

	/**
	 * Loads a pretty HTML file and loads it with the exception data passed
	 * to it in.
	 *
	 * @param Exception The exception to parse
	 * @return void
	 * @static
	 */
	public static function handleStatic(Exception $exception)
	{
		$templatefile = __DIR__ . DIRECTORY_SEPARATOR . 'exception-template.php';

		if(file_exists($templatefile))
		{
			$message = $exception->getMessage();
			$code = $exception->getCode();
			$file = $exception->getTrace()[0]['file'];
			$line = $exception->getTrace()[0]['line'];
			$trace = $exception->getTraceAsString();
			$previous = $exception->getPrevious();
			$exceptionType = get_class($exception);

			require_once($templatefile);
		}
	}
}
