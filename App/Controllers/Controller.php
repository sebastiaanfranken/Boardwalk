<?php
namespace App\Controllers;

abstract class Controller
{
	public function __construct(){}

	/**
	 * This is a "master" before function. It doesn't matter which child controller you're using, this
	 * function _will_ get called.
	 *
	 * Useful for a query, or a log
	 */
	public function before()
	{
		$log = new \App\Models\Log();
		$log->ip = $_SERVER['REMOTE_ADDR'];
		$log->url = $_SERVER['REQUEST_URI'];
		$log->timestamp = timestamp('Y-m-d H:i:s');
		$log->create();
	}
}