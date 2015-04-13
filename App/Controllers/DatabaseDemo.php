<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\View;

class DatabaseDemo extends Controller
{
	public function getIndex()
	{
		$view = new View('database-demo/index');
		return $view->make();
	}

	public function postIndex()
	{

	}
}