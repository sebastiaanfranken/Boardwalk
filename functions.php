<?php

/*
 * Every system, support or other important function goes here
 */

function pr($input)
{
	return '<pre>' . print_r($input, true) . '</pre>';
}

function root()
{
	return __DIR__ . DIRECTORY_SEPARATOR;
}

function app()
{
	return root() . 'app' . DIRECTORY_SEPARATOR;
}

function vendor()
{
	return root() . 'vendor' . DIRECTORY_SEPARATOR;
}