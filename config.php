<?php
return [
    'type' => 'Plugin',
    'title' => 'IPリミッター',
    'description' => 'IPアドレスを指定してコンテンツへのアクセスを制限します。',
    'author' => 'catchup',
    'url' => 'https://catchup.co.jp/',
    'adminLink' => ['prefix' => 'Admin', 'plugin' => 'CuIpLimiter', 'controller' => 'IpLimiterConfigs', 'action' => 'index'],
    'installMessage' => '',
];
