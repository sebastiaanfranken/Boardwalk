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
	 */
	public function __construct()
	{
		set_exception_handler(array($this, 'handle'));
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
			$file = $exception->getFile();
			$line = $exception->getLine();
			$trace = $exception->getTraceAsString();
			$previous = $exception->getPrevious();

			require_once($templatefile);
		}
	}
}
