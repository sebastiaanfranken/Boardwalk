<?php
namespace App\Controllers;

use App\Controllers\Controller;

class TextDemo extends Controller
{
	public function getIndex()
	{
		return (new \Boardwalk\View('text-demo/index'))->make();
	}
}