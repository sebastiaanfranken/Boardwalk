<?php

return array(
	array('GET', '/', 'Index@getIndex', 'home'),
	array('GET', '/text-demo', 'TextDemo@getIndex'),
	array('POST', '/text-demo', 'TextDemo@postIndex'),
	array('GET', '/database-demo', 'DatabaseDemo@getIndex'),
	array('POST', '/database-demo', 'DatabaseDemo@postIndex'),
	array('GET', '/database-demo/rekey', 'DatabaseDemo@getRekey'),
	array('GET', '/database-demo/query', 'DatabaseDemo@getQuery'),
	array('GET', '/helpers-demo', 'HelpersDemo@getIndex'),
	array('GET', '/helpers-demo/convert', 'HelpersDemo@getConversion'),
	array('POST', '/helpers-demo/convert', 'HelpersDemo@postConversion')
);