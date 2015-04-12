<?php
namespace App\Controllers;

use App\Controllers\Controller;

class Index extends Controller
{
	public function getIndex()
	{
		$log = new \App\Models\Log();

		print pr($log);

		return 'Hallo bezoeker!';
	}
}