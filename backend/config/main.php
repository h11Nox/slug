<?php
$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'id' => 'app-backend',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'backend\controllers',
	'bootstrap' => ['log'],
	'modules' => [
		'deck' => [
			'class' => 'backend\modules\deck\Module',
		],
	],
	'components' => [
		'user' => [
			'identityClass' => 'backend\models\User',
			'enableAutoLogin' => true,
			'identityCookie' => [
				'name' => '_backend'
			]
		],
		'session' => [
			'name' => '_backendSessionId'
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'request'=>[
			'baseUrl'=>'/backend',
		],
		'urlManager'=>[
			'scriptUrl'=>'/backend/index.php',
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
	],
	'params' => $params,
];
