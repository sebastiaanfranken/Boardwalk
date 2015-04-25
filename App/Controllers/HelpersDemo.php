<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Boardwalk\View;
use Boardwalk\Utilities\CharacterConverter;

class HelpersDemo extends Controller
{
	public function getIndex()
	{
		$converter = new CharacterConverter();
		$converter->addRule('\t', '----');
		$converter->setContent('Dit is een tekst met een \t tab');

		return pr($converter->convert());
	}

	public function getConversion()
	{

	}

	public function postConversion()
	{

	}
}