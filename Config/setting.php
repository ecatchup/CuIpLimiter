<?php
/**
 * IP Limitter システムナビ
 *
 * @package ip_limiter.config
 */
$config['BcApp.adminNavi.ip_limiter'] = array(
		'name'		=> 'IPリミッター プラグイン',
		'contents'	=> array(
			array('name' => '設定',
				'url' => array('admin' => true, 'plugin' => 'ip_limiter', 'controller' => 'ip_limiter_configs', 'action' => 'index'))
	)
);
