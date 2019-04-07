<?php

return [
	'actions' => [
		'questions/save' => [],
		'questions/delete' => [],
		'questions/mark' => [],
	],
	'routes' => [
		'collection:object:questions' => [
			'path' => '/questions',
			'resource' => 'questions/all',
		],
		'all:object:questions' => [
			'path' => '/questions/all',
			'resource' => 'questions/all',
		],
		'owner:object:questions' => [
			'path' => '/questions/owner',
			'resource' => 'questions/owner',
		],
		'friends:object:questions' => [
			'path' => '/questions/friends',
			'resource' => 'questions/friends',
		],
		'view:object:questions' => [
			'path' => '/questions/view/{guid}',
			'resource' => 'questions/view',
		],
		'edit:object:questions' => [
			'path' => '/questions/edit/{guid}',
			'resource' => 'questions/edit',
		],
		'add:object:questions' => [
			'path' => '/questions/add/{guid}',
			'resource' => 'questions/add',
		],
		'group:object:questions' => [
			'path' => '/questions/group/{guid}/all',
			'resource' => 'questions/owner',
		],
	],
];
