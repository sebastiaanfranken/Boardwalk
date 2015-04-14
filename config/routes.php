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
	),
	'database-demo' => array(
		'index' => array(
			'get' => array('DatabaseDemo', 'getIndex'),
			'post' => array('DatabaseDemo', 'postIndex')
		),
		'rekey' => array(
			'get' => array('DatabaseDemo', 'getRekey')
		),
		'query' => array(
			'get' => array('DatabaseDemo', 'getQuery')
		)
	)
);