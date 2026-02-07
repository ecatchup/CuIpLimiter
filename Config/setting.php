<?php
$config = [
	'BcApp' => [
		/**
		 * システムナビ
		 */
		'adminNavigation' => [
			'Systems' => [
				'IpLimiter' => [
					'title' => __d('baser', 'IP制限設定'),
					'type' => 'system',
					'url' => [
						'admin' => true,
						'plugin' => 'cu_ip_limiter',
						'controller' => 'ip_limiter_configs',
						'action' => 'index'
					]
				]
			]
		]
	],
	'IpLimiter' => [
		/**
		 * 基本許可IP
		 * 管理画面で設定するものとは別に指定する
		 * 配列で複数指定できる
		 */
		'basicAllowedIp' => []
	]
];
