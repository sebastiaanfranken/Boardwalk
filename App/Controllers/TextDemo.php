<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\Utilities\Text;
use Boardwalk\View;

class TextDemo extends Controller
{
	public function getIndex()
	{

		$variables = array(
			'home' => url('index')
		);
		$view = new View('text-demo/index');

		return $view->make();
	}

	public function postIndex()
	{
		$variables = array(
			'home' => url('index'),
			'result' => (new Text())->convert($_POST['text'])
		);
		$view = new View('text-demo/index', $variables);

		return $view->make(); 
	}
}