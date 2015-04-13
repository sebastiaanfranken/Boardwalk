<?php
namespace App\Controllers;

use App\Controllers\Controller;

class Index extends Controller
{
	public function getIndex()
	{
		$dontlog = array('::1', '127.0.0.1');

		if(!in_array($_SERVER['REMOTE_ADDR'], $dontlog))
		{
			$log = new \App\Models\Log();
			$log->ip = $_SERVER['REMOTE_ADDR'];
			$log->url = $_SERVER['REQUEST_URI'];
			$log->timestamp = timestamp('Y-m-d H:i:s');
			$log->create();
		}

		$attributes = array(
			'textDemoLink' => url('TextDemo', 'getIndex'),
			'databaseDemoLink' => url('DatabaseDemo', 'getIndex')
		);

		$view = new \Boardwalk\View('index', $attributes);
		return $view->make();
	}
}