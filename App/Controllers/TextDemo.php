<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\Utilities\Text;

class TextDemo extends Controller
{
	public function getIndex()
	{
		return (new \Boardwalk\View('text-demo/index'))->make();
	}

	public function postIndex()
	{
		$content = (new Text())->convert($_POST['text']);

		return $content;
	}
}