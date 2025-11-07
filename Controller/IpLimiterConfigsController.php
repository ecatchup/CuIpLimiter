<?php
/* SVN FILE: $Id$ */
/**
 * [IpLimiter] 設定ページ
 *
 * PHP version 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2011 - 2012, Catchup, Inc.
 * @link			http://www.e-catchup.jp Catchup, Inc.
 * @package			ip_limiter.controllers
 * @since			Baser v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT lincense
 */
App::import('Controller', 'Plugins');
class IpLimiterConfigsController extends AppController {
/**
 * コントローラー名
 * @var string
 * @access public
 */
	public $name = 'IpLimiterConfigs';
/**
 * モデル
 * @var array
 * @access public
 */
	public $uses = array('Plugin', 'CuIpLimiter.IpLimiterConfig');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	public $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'IPリミッター管理', 'url' => array('plugin' => 'cu_ip_limiter', 'controller' => 'ip_limiter_configs', 'action' => 'index'))
	);
/**
 * IPリミッター設定
 */
	public function admin_index() {

		if(!$this->request->data) {
			$this->request->data = array('IpLimiterConfig' => $this->IpLimiterConfig->findExpanded());
		} else {
			$this->IpLimiterConfig->set($this->request->data);
			if($this->IpLimiterConfig->validates()) {
				$this->IpLimiterConfig->saveKeyValue($this->request->data);
				$this->setMessage('IPリミッターの設定を保存しました。', false, true);
				$this->redirect(['action' => 'index']);
			}
		}
		$this->pageTitle = 'IPリミッター設定';
		$this->help = 'ip_limiter_configs_index';
		$this->render('index');

	}

}
