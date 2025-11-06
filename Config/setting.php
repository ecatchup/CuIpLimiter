<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

return [
    'BcApp' => [
        'adminNavigation' => [
            'Systems' => [
                'Plugin' => [
                    'menus' => [
                        'CuIpLimiter' => [
                            'title' => 'IPリミッター設定',
                            'url' => [
                                'prefix' => 'Admin',
                                'plugin' => 'CuIpLimiter',
                                'controller' => 'IpLimiterConfigs',
                                'action' => 'index'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
