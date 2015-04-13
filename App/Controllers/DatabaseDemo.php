<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\View;

class DatabaseDemo extends Controller
{
	public function getIndex()
	{
		$log = new \App\Models\Log();
		$variables = array(
			'loglines' => $log->fetchAll()
		);

		$view = new View('database-demo/index', $variables);
		return $view->make();
	}

	public function postIndex()
	{
		$log = new \App\Models\Log();
		$log->delete('request_method', '=', 'POST');
		$log->close();

		$log = new \App\Models\Log();
		$log->delete('url', '=', '/database-demo');
		$log->close();

		//return header('Location: /database-demo');
		return header('Location: ' . url('DatabaseDemo', 'getIndex'));
	}
}