<?php

	return [
		// Define a redirect render 
		'redirect_loged' => 'profile',
		'redirect_unloged' => 'login',
		// If access control is seted, this render require be logged
		'logged' => [
			'profile',
			'crop',
			'deactive_account',
			'logout',
			'publish',
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
			'terms',
			'Lidiun\Redirecting',
			'',
		],
	];