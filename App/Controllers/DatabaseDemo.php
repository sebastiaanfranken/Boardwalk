<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\View;

use App\Models\Log;

class DatabaseDemo extends Controller
{
	public function getIndex()
	{
		$log = new Log();
		$variables = array(
			'loglines' => $log->all(),
			'counter' => $log->count(),
			'uniqueCounter' => $log->countDistinct('timestamp')
		);

		$view = new View('database-demo/index', $variables);
		return $view->make();
	}

	public function postIndex()
	{
		$log = new Log();
		$log->delete('request_method', '=', 'POST');
		$log->close();

		$log = new Log();
		$log->delete('url', '=', '/database-demo');
		$log->close();

		return header('Location: ' . url('DatabaseDemo', 'getIndex'));
	}

	public function getRekey()
	{
		$log = new Log();
		$log->rekey();

		return header('Location: ' . url('DatabaseDemo', 'getIndex'));
	}

	public function getQuery()
	{
		$log = new Log();

		return pr(
			$log->get('request_method', '=', 'GET')->all()
		);
	}
}