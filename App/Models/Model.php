<?php
namespace App\Models;

use Exception;
use mysqli;

abstract class Model
{
	protected $connection;

	public function __construct()
	{
		
	}
}