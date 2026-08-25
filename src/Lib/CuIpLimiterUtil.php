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
        return trim((string)$ipaddr);
    }

    /**
     * 現在のクライアントIPがアクセスを許可されているか
     *
     * - 管理画面で設定した allowed_ip が空の場合は制限なし（全許可）
     * - 設定ファイルの `IpLimiter.basicAllowedIp`（配列・管理画面とは別の基本許可IP）があれば allowed_ip と合わせて判定する
     * - `*` はグループ指定（例: 192.168.0.*）、カンマ区切りで複数指定可
     *
     * @return bool
     */
    public static function isAllowed()
    {
        $IpLimiterConfigsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs');
        $entity = $IpLimiterConfigsTable->getKeyValue();
        if (!$entity || empty($entity['allowed_ip'])) {
            return true;
        }
        $allowedIps = explode(',', $entity['allowed_ip']);
        $basicAllowedIp = Configure::read('IpLimiter.basicAllowedIp');
        if (!empty($basicAllowedIp) && is_array($basicAllowedIp)) {
            $allowedIps = array_merge($basicAllowedIp, $allowedIps);
        }
        $clientIp = self::getClientIp();
        foreach($allowedIps as $allowedIp) {
            $allowedIp = trim((string)$allowedIp);
            if ($allowedIp === '') continue;
            $pattern = str_replace('\*', '.+?', preg_quote($allowedIp, '/'));
            if (preg_match('/' . $pattern . '/', $clientIp)) {
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
