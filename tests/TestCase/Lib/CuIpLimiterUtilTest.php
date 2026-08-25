<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

namespace CuIpLimiter\Test\TestCase\Lib;

use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use CuIpLimiter\Lib\CuIpLimiterUtil;

/**
 * CuIpLimiterUtilTest
 */
class CuIpLimiterUtilTest extends BcTestCase
{

    /**
     * 退避した $_SERVER
     * @var array
     */
    private array $serverBackup = [];

    /**
     * 退避した Configure
     * @var mixed
     */
    private $basicAllowedIpBackup = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
        $this->basicAllowedIpBackup = Configure::read('IpLimiter.basicAllowedIp');
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_CLIENT_IP'], $_SERVER['HTTP_CLIENTADDRESS']);
        $_SERVER['REMOTE_ADDR'] = '192.168.0.5';
        Configure::write('IpLimiter.basicAllowedIp', []);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        Configure::write('IpLimiter.basicAllowedIp', $this->basicAllowedIpBackup);
        parent::tearDown();
    }

    /**
     * 設定値を保存する
     */
    private function saveConfig(array $values): void
    {
        TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs')->saveKeyValue($values);
    }

    /**
     * test getClientIP
     */
    public function testGetClientIP(): void
    {
        $this->assertEquals('192.168.0.5', CuIpLimiterUtil::getClientIP());

        // safe=true では X-Forwarded-For を見ない
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '203.0.113.9, 10.0.0.1';
        $this->assertEquals('192.168.0.5', CuIpLimiterUtil::getClientIP());
        // safe=false では X-Forwarded-For の先頭
        $this->assertEquals('203.0.113.9', CuIpLimiterUtil::getClientIP(false));

        // HTTP_CLIENTADDRESS は safe に関わらず最優先（先頭のみ・4系互換）
        $_SERVER['HTTP_CLIENTADDRESS'] = '198.51.100.7, 10.0.0.1';
        $this->assertEquals('198.51.100.7', CuIpLimiterUtil::getClientIP());
        $this->assertEquals('198.51.100.7', CuIpLimiterUtil::getClientIP(false));
        unset($_SERVER['HTTP_CLIENTADDRESS']);

        // REMOTE_ADDR が無い（CLI 等）場合でもエラーにならず空文字
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']);
        $this->assertSame('', CuIpLimiterUtil::getClientIP());
    }

    /**
     * 設定が無い／allowed_ip が空なら制限なし
     */
    public function testIsAllowedWithoutConfig(): void
    {
        $this->assertTrue(CuIpLimiterUtil::isAllowed());
        $this->saveConfig(['allowed_ip' => '', 'limit_folders' => 'secret']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());
    }

    /**
     * allowed_ip の完全一致・カンマ区切り・ワイルドカード
     */
    public function testIsAllowedByAllowedIp(): void
    {
        $this->saveConfig(['allowed_ip' => '10.0.0.1']);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());

        $this->saveConfig(['allowed_ip' => '192.168.0.5']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());

        $this->saveConfig(['allowed_ip' => '10.0.0.1,192.168.0.5']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());

        $this->saveConfig(['allowed_ip' => '10.0.0.1, 192.168.0.5 ']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed(), '空白入りのカンマ区切りも許容');

        $this->saveConfig(['allowed_ip' => '192.168.0.*']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());

        $this->saveConfig(['allowed_ip' => '192.168.1.*']);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());
    }

    /**
     * 【既知仕様】パターンは非アンカー一致（部分一致）である
     *
     * 4系からの互換仕様として、`10.0.0.1` は `110.0.0.12` のような
     * 「含む」IPにも一致する（アンカー ^$ を付けない）。
     * 仕様変更（完全一致化）すると4系と挙動が割れるため、意図的に維持している。
     */
    public function testIsAllowedMatchesPartially(): void
    {
        $_SERVER['REMOTE_ADDR'] = '110.0.0.12';
        $this->saveConfig(['allowed_ip' => '10.0.0.1']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed(), '非アンカー一致（既知仕様）が変わっている');
    }

    /**
     * 末尾カンマ等で空のパターンが混ざっても全許可にならない
     */
    public function testIsAllowedIgnoresEmptyPattern(): void
    {
        $this->saveConfig(['allowed_ip' => '10.0.0.1,']);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());
    }

    /**
     * 設定ファイルの基本許可IP（IpLimiter.basicAllowedIp）
     */
    public function testIsAllowedByBasicAllowedIp(): void
    {
        $this->saveConfig(['allowed_ip' => '10.0.0.1']);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());

        Configure::write('IpLimiter.basicAllowedIp', ['192.168.0.*']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());

        Configure::write('IpLimiter.basicAllowedIp', ['203.0.113.1', '192.168.0.5']);
        $this->assertTrue(CuIpLimiterUtil::isAllowed());

        Configure::write('IpLimiter.basicAllowedIp', ['203.0.113.1']);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());
    }

    /**
     * basicAllowedIp が未設定（null / []）でも全許可にならない
     */
    public function testIsAllowedWhenBasicAllowedIpIsEmpty(): void
    {
        $this->saveConfig(['allowed_ip' => '10.0.0.1']);
        Configure::write('IpLimiter.basicAllowedIp', []);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());
        Configure::write('IpLimiter.basicAllowedIp', null);
        $this->assertFalse(CuIpLimiterUtil::isAllowed());
    }

    /**
     * test hasLimitFolders
     */
    public function testHasLimitFolders(): void
    {
        $this->assertFalse(CuIpLimiterUtil::hasLimitFolders());
        $this->saveConfig(['limit_folders' => '']);
        $this->assertFalse(CuIpLimiterUtil::hasLimitFolders());
        $this->saveConfig(['limit_folders' => 'secret,member']);
        $this->assertTrue(CuIpLimiterUtil::hasLimitFolders());
    }

    /**
     * test isLimitFolder
     */
    public function testIsLimitFolder(): void
    {
        $this->saveConfig(['limit_folders' => 'secret,member']);

        $_SERVER['REQUEST_URI'] = '/secret/page?x=1';
        $this->assertTrue(CuIpLimiterUtil::isLimitFolder());
        $_SERVER['REQUEST_URI'] = '/member/';
        $this->assertTrue(CuIpLimiterUtil::isLimitFolder());
        $_SERVER['REQUEST_URI'] = '/about';
        $this->assertFalse(CuIpLimiterUtil::isLimitFolder());
        // トップ（フォルダ無し）は制限対象外
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertFalse(CuIpLimiterUtil::isLimitFolder());
    }

    /**
     * test getRedirectUrl
     */
    public function testGetRedirectUrl(): void
    {
        $this->assertNull(CuIpLimiterUtil::getRedirectUrl());
        $this->saveConfig(['redirect_url' => 'https://example.com/']);
        $this->assertEquals('https://example.com/', CuIpLimiterUtil::getRedirectUrl());
    }

    /**
     * test getUrlParamFromEnv
     */
    public function testGetUrlParamFromEnv(): void
    {
        $_SERVER['REQUEST_URI'] = '/secret/page?x=1';
        $this->assertEquals('secret/page', CuIpLimiterUtil::getUrlParamFromEnv());
        unset($_SERVER['REQUEST_URI']);
        $this->assertSame('', CuIpLimiterUtil::getUrlParamFromEnv());
    }

}
