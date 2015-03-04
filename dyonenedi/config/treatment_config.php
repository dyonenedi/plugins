<?php	
	// Define rules to treat parameters passed by $_GET or $_POST 
	return [
		'name' => [
			'strip_tags',
			'ucwords',
		],
		'user' => [
			'strip_tags',
			'strtolower',
		],
		'email' => [
			'strip_tags',
			'strtolower',
		],
		'password' => [
			'strip_tags',
		],
		'text' => [
			'strip_tags',
			'nl2br',
		],
		'message' => [
			'strip_tags',
			'nl2br',
		],
	];
