<?php
namespace App\Controllers;

use App\Controllers\Controller;

class Index extends Controller
{
	public function getIndex()
	{
		$log = new \App\Models\Log();
		$log->ip = $_SERVER['REMOTE_ADDR'];
		$log->url = $_SERVER['REQUEST_URI'];
		$log->timestamp = timestamp('Y-m-d H:i:s');
		$log->create();

		return (new \Boardwalk\View('index'))->make();
	}
}