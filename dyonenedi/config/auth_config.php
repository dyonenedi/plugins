<?php

	return [
		// Define a redirect render 
		'redirect_loged' => 'publication',
		'redirect_unloged' => 'login',
		// If access control is seted, this render require be logged
		'logged' => [
			'account',
			'crop',
			'deactive_account',
			'logout',
			'my_publication',
			'my_sponsored',
			'publish',
			'sponsored',
		],
		// If access control is seted, this render require be unlogged
		'unlogged' => [
			'login',
			'recover',
			'recovering',
			'register',
		],
		// If access control is seted, this render doesn't matter about the log
		'whatever' => [
			'about',
			'contact',
			'support',
			'supporting',
			'terms',
			'thankyou',
			'detail',
			'message',
			'publication',
			'user',
			'test',
			'Lidiun\Redirecting',
			'disallowed',
			'illegal',
			'',
		],
	];