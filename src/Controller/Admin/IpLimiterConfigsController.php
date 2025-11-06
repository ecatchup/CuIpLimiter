<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

namespace CuIpLimiter\Controller\Admin;

/**
 * IpLimiterConfigsController
 */
class IpLimiterConfigsController extends \BaserCore\Controller\Admin\BcAdminAppController
{
    /**
     * IPリミッター設定
     */
    public function index()
    {
        $entity = $this->IpLimiterConfigs->newEntity($this->IpLimiterConfigs->getKeyValue());
        if ($this->getRequest()->is('post')) {
            $entity = $this->IpLimiterConfigs->patchEntity(
                $entity,
                $this->getRequest()->getData(),
                ['validate' => 'keyValue']
            );
            if (!$entity->hasErrors()) {
                $this->IpLimiterConfigs->saveKeyValue($entity->toArray());
                $this->BcMessage->setInfo('IPリミッターの設定を保存しました。');
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError('入力エラーです。内容を修正してください。');
            }
        }
        $this->set(['entity' => $entity]);
    }
}
