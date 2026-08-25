<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

namespace CuIpLimiter\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * IpLimiterConfigsControllerTest
 */
class IpLimiterConfigsControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * 管理画面 URL
     */
    private const INDEX_URL = '/baser/admin/cu-ip-limiter/ip_limiter_configs/index';

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest(self::INDEX_URL));
    }

    /**
     * エラーログのパス
     */
    private function errorLogPath(): string
    {
        return TESTS . 'TestApp' . DS . 'logs' . DS . 'error.log';
    }

    /**
     * 設定画面の描画
     */
    public function testIndexGet(): void
    {
        $logPath = $this->errorLogPath();
        if (file_exists($logPath)) file_put_contents($logPath, '');

        $this->get(self::INDEX_URL);
        $this->assertResponseSuccess();
        $this->assertResponseContains('IP制限設定');
        // フォーム項目（control() が生成する id）と JS が掴む保存ボタン id
        foreach (['allowed-ip', 'limit-folders', 'redirect-url', 'BtnSave'] as $id) {
            $this->assertResponseContains('id="' . $id . '"', "id {$id} が描画されていない");
        }
        // 描画 200 でも PHP warning（未定義変数等）が握り潰されていないこと
        $log = file_exists($logPath) ? file_get_contents($logPath) : '';
        $this->assertStringNotContainsString('Undefined variable', $log);
        $this->assertStringNotContainsString('warning:', $log);
    }

    /**
     * 設定の保存
     */
    public function testIndexPost(): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post(self::INDEX_URL, [
            'allowed_ip' => '192.168.0.*,10.0.0.1',
            'limit_folders' => 'secret',
            'redirect_url' => 'https://example.com/',
        ]);
        $this->assertRedirect(['plugin' => 'CuIpLimiter', 'prefix' => 'Admin', 'controller' => 'IpLimiterConfigs', 'action' => 'index']);
        $this->assertFlashMessage('IP制限の設定を保存しました。');

        $saved = TableRegistry::getTableLocator()->get('CuIpLimiter.IpLimiterConfigs')->getKeyValue();
        $this->assertEquals('192.168.0.*,10.0.0.1', $saved['allowed_ip']);
        $this->assertEquals('secret', $saved['limit_folders']);
        $this->assertEquals('https://example.com/', $saved['redirect_url']);

        // 保存後の再描画で値が入っていること
        $this->get(self::INDEX_URL);
        $this->assertResponseSuccess();
        $this->assertResponseContains('value="192.168.0.*,10.0.0.1"');
    }

    /**
     * 未ログインはログイン画面へ
     */
    public function testIndexRequiresLogin(): void
    {
        // setUp のログインセッションを無効化
        $this->session([\Cake\Core\Configure::read('BcPrefixAuth.Admin.sessionKey') => null]);
        $this->get(self::INDEX_URL);
        $this->assertRedirectContains('/baser/admin/baser-core/users/login');
    }

}
