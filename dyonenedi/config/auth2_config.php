<?php

	return [
		// Define a redirect render 
		'redirect_loged' => 'my_dungeon',
		'redirect_unloged' => 'login',
		// If access control is seted, this render require be logged
		'logged' => [
			'deactive_account',
			'logout',
			'my_dungeon',
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
			'',
			'Lidiun\Redirecting',
		],
	];