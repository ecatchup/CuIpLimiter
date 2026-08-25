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
        /**
         * システムナビ
         */
        'adminNavigation' => [
            'Systems' => [
                'IpLimiter' => [
                    'title' => 'IP制限設定',
                    'type' => 'system',
                    'url' => [
                        'prefix' => 'Admin',
                        'plugin' => 'CuIpLimiter',
                        'controller' => 'IpLimiterConfigs',
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
         * 配列で複数指定できる（* でグループ指定可）
         * 例: ['203.0.113.10', '192.168.0.*']
         * ※ 実際のIPは本プラグインに書かず、アプリの config/setting.php に同じキーで設定する。
         *   判定は本プラグインの bootstrap 時点で行われるため、他プラグインの setting.php に設定する場合は、
         *   そのプラグインが CuIpLimiter より【先に】ロードされている必要がある（plugins.priority で順序を保証すること）。
         * ※ 管理画面「許可するIPアドレス」が空の場合は制限自体が無効で、この値は参照されない。
         */
        'basicAllowedIp' => []
    ]
];
