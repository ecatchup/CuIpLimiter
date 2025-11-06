<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

namespace CuIpLimiter\Event;

use Cake\Http\Exception\NotFoundException;
use CuIpLimiter\Lib\CuIpLimiterUtil;

/**
 * CuIpLimiterControllerEventListener
 */
class CuIpLimiterControllerEventListener extends \BaserCore\Event\BcControllerEventListener
{

    /**
     * Events
     * @var string[]
     */
    public $events = ['startup'];

    /**
     * startup
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function startup(\Cake\Event\Event $event)
    {
        $controller = $event->getSubject();

        if(CuIpLimiterUtil::isAllowed()) return;
        if(!CuIpLimiterUtil::hasLimitFolders()) {
            throw new NotFoundException('見つかりませんでした。');
        }
        if(!CuIpLimiterUtil::isLimitFolder()) return;
        $redirectUrl = CuIpLimiterUtil::getRedirectUrl();
        if ($redirectUrl) {
            $controller->setResponse(
                $controller->getResponse()->withStatus(302)->withHeader('Location', $redirectUrl)
            );
            return $controller->getResponse();
        } else {
            throw new NotFoundException('見つかりませんでした。');
        }
    }

}
