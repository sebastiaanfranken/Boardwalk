<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\View;

class Index extends Controller
{
	public function getIndex()
	{
		$attributes = array(
			'textDemoLink' => url('TextDemo', 'getIndex'),
			'databaseDemoLink' => url('DatabaseDemo', 'getIndex')
		);

		$view = new \Boardwalk\View('index', $attributes);
		return $view->make();
	}
}
