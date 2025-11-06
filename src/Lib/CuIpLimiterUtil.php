<?php

namespace CuIpLimiter\Lib;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;

class CuIpLimiterUtil
{

    /**
     * クライアントのIPアドレスを取得する
     * @param $safe
     * @return string
     */
    public static function getClientIP($safe = true)
    {
        if (!$safe && env('HTTP_X_FORWARDED_FOR') != null) {
            $ipaddr = preg_replace('/(?:,.*)/', '', env('HTTP_X_FORWARDED_FOR'));
        } else {
            if (env('HTTP_CLIENT_IP') != null) {
                $ipaddr = env('HTTP_CLIENT_IP');
            } else {
                $ipaddr = env('REMOTE_ADDR');
            }
        }

        if (env('HTTP_CLIENTADDRESS') != null) {
            $tmpipaddr = env('HTTP_CLIENTADDRESS');

            if (!empty($tmpipaddr)) {
                $ipaddr = preg_replace('/(?:,.*)/', '', $tmpipaddr);
            }
        }
        return trim($ipaddr);
    }

    public static function isAllowed()
    {
        $IpLimiterConfigsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs');
        $entity = $IpLimiterConfigsTable->getKeyValue();
        if (!$entity || empty($entity['allowed_ip'])) {
            return true;
        }
        $allowedIp = preg_quote($entity['allowed_ip']);
        $patterns = str_replace("\*", '.+?', $allowedIp);
        $patterns = explode(',', $patterns);
        foreach($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/', self::getClientIp())) {
                return true;
            }
        }
        return false;
    }

    public static function hasLimitFolders()
    {
        $IpLimiterConfigsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs');
        $entity = $IpLimiterConfigsTable->getKeyValue();
        if ($entity && !empty($entity['limit_folders'])) {
            return true;
        }
        return false;
    }

    public static function isLimitFolder()
    {
        $folder = explode('/', self::getUrlParamFromEnv());
        if (!empty($folder[0])) {
            $folder = $folder[0];
        } else {
            return false;
        }
        $IpLimiterConfigsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs');
        $entity = $IpLimiterConfigsTable->getKeyValue();
        if ($entity && !empty($entity['limit_folders'])) {
            $limitFolders = explode(',', $entity['limit_folders']);
            if (in_array($folder, $limitFolders)) {
                return true;
            }
            return false;
        }
        return true;
    }

    public static function getRedirectUrl()
    {
        $IpLimiterConfigsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs');
        $entity = $IpLimiterConfigsTable->getKeyValue();
        if ($entity && !empty($entity['redirect_url'])) {
            return $entity['redirect_url'];
        }
        return null;
    }

    /**
     * 環境変数よりURLパラメータを取得する
     * - プレフィックスは除外する
     * - GETパラメーターは除外する
     *
     * 《注意》
     * bootstrap 実行後でのみ利用可
     */
    public static function getUrlParamFromEnv()
    {
        $url = self::getUrlFromEnv();
        $url = preg_replace('/^\//', '', $url);
        if (strpos($url, '?') !== false) {
            [$url] = explode('?', $url);
        }
        return $url;
    }

    /**
     * 環境変数よりURLを取得する
     *
     * スマートURLオフ＆bootstrapのタイミングでは、$_GET['url']が取得できてない為、それをカバーする為に利用する
     * ＊ 先頭のスラッシュは除外する
     * ＊ baseUrlは除外する
     */
    public static function getUrlFromEnv()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return '';
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
        }
        $appBaseUrl = Configure::read('App.baseUrl');
        if ($appBaseUrl) {
            $base = dirname($appBaseUrl);
            if (strpos($requestUri, $appBaseUrl) !== false) {
                $parameter = str_replace($appBaseUrl, '', $requestUri);
            } else {
                // トップページ
                $parameter = str_replace($base . '/', '', $requestUri);
            }
        } else {
            if (strpos($requestUri, '?')) {
                $aryRequestUri = explode('?', $requestUri);
                $requestUri = $aryRequestUri[0];
            }
            if (preg_match('/^' . str_replace('/', '\/', BcUtil::baseUrl()) . '/is', $requestUri)) {
                $parameter = preg_replace('/^' . str_replace('/', '\/', BcUtil::baseUrl()) . '/is', '', $requestUri);
            } else {
                $parameter = $requestUri;
            }
        }
        return preg_replace('/^\//', '', $parameter);
    }

}
