<?php
return array(
	'index' => array(
		'get' => array('Index', 'getIndex')
	),
	'text-demo' => array(
		'index' => array(
			'get' => array('TextDemo', 'getIndex'),
			'post' => array('TextDemo', 'postIndex')
		)
	)
);