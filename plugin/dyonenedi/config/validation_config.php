<?php
	// Define rules to validate parameters passed by $_GET or $_POST 
	return [
		'free' => [
			'free' => true,
		],
		'name' => [
			'required' => true,
			'length' => '35',
			'type' => 'string',
			'word' => true,
			'caracter' => false,
			'number' => false,
		],
		'user' => [
			'required' => true,
			'length' => '3,35',
			'type' => 'string',
			'word' => true,
			'space' => false,
			'caracter' => true,
			'number' => true,
			'expressionp' => '[a-z0-9\._-]',
		],
		'password' => [
			'required' => true,
			'length' => '8,16',
			'type' => 'string',
			'space' => false,
			'caracter' => false,
			'number' => true,
		],
		'email' => [
			'required' => true,
			'length' => '200',
			'type' => 'string',
			'expression' => '^([\w\-]+\.)*[\w\- ]+@([\w\- ]+\.)+([\w\-]{2,3})$',
		],
		'text' => [
			'required' => true,
			'length' => '500',
			'type' => 'string',
		],
		'age' => [
			'required' => true, 
			'length' => '3',
			'type' => 'int',
		],
		'remember' => [
			'required' => true,
			'type' => 'int',
		],
		'url' => [
			'expression' => '^((http)|(https)|(ftp)):\/\/([\- \w]+\.)+\w{2,3}(\/ [%\-\w]+',
		],
		'ip' => [
			'expression' => '\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b',
		],
		'date' => [
			'expression' => '^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))',
		],
		'time' => [
			'expression' => '^([0|1|2]{1}\d):([0|1|2|3|4|5]{1} \d)$',
		],
		'number' => [
			'type' => 'int',
		]
		,'phone' => [
			'expression' => '\((10)|([1-9][1-9])\) [2-9][0-9]{3}-[0-9]{4}',
		],
		'string' => [
			'type' => 'string',
		],
	];
