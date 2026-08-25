# CuIpLimiter

IP アドレスを指定してコンテンツへのアクセスを制限する baserCMS 用プラグインです。

## 特徴

- 管理画面から許可する IP アドレスを設定し、それ以外からのアクセスを遮断（404）します
- 許可 IP はカンマ区切りで複数指定でき、`*` によるグループ指定に対応しています（例: `192.168.0.*`）
- 制限対象を特定のフォルダ（URL の第1階層）のみに絞ることができます
- 遮断時にリダイレクトする URL を指定できます（未指定の場合は 404）
- コンソール（`bin/cake`・CRON）実行時は制限をスキップします

## インストール

`plugins/CuIpLimiter` に配置し、管理画面の「プラグイン管理」から有効化します。

## 使い方

管理画面の `システム > IP制限設定` で以下を設定します。

| 項目 | 内容 |
| --- | --- |
| 許可するIPアドレス | 空の場合は制限なし。設定すると一致しない IP からのアクセスを遮断 |
| フォルダー指定 | 指定したフォルダのみを制限対象にする（カンマ区切り） |
| リダイレクト先URL | 遮断時の転送先。未指定の場合は 404 |

### 基本許可IP（設定ファイル）

管理画面の設定とは別に、`IpLimiter.basicAllowedIp` で常に許可する IP を追加できます。
本プラグインの `config/setting.php` は既定値（空）のみを持ち、実際の IP は次のいずれかのファイルに設定します。

- アプリの `config/setting.php`（推奨。`config/setting.example.php` をコピーして作成）: 全プラグインより先に読み込まれるため、ロード順を気にせず確実に反映されます。ローカル環境のリバースプロキシ IP など環境固有の値に向いています（baserCMS の既定ではコミット対象外のファイルです）
- サイト固有プラグインの `config/setting.php`: サイトの全環境に共通する値（運営側の固定 IP など）に向いています

複数のファイルに設定した場合、値は結合されます（すべて許可 IP として扱われます）。

> [!IMPORTANT]
> `basicAllowedIp` は管理画面の「許可するIPアドレス」に追加される常時許可分です。制限の有効／無効は管理画面の「許可するIPアドレス」で決まるため、そちらが空だと `basicAllowedIp` を設定していても制限は行われません（`basicAllowedIp` 単独では制限は有効になりません）。制限を有効にするには、管理画面の「許可するIPアドレス」に 1 つ以上の IP を設定してください。

```php
// 例: アプリの config/setting.php（または他プラグインの config/setting.php）
return [
    'IpLimiter' => [
        'basicAllowedIp' => [
            '203.0.113.10',
            '192.168.0.*',
        ],
    ],
];
```

> [!NOTE]
> 判定は本プラグインの bootstrap 時点で行われるため、他プラグインの `config/setting.php` に設定する場合は、そのプラグインが CuIpLimiter より先にロードされている必要があります（`plugins` テーブルの priority で順序を調整してください）。アプリの `config/setting.php` に設定する場合はこの制約はありません。

## 開発

### ユニットテストの実行

本プラグインは単体（standalone）でテストを実行できます。

1. 依存パッケージをインストールします（プラグインディレクトリで実行）。

    ```shell
    composer install
    ```

2. テスト用の DB 接続情報を設定します。`.env` は gitignore 対象です。

    ```shell
    cp tests/TestApp/config/.env.example tests/TestApp/config/.env
    ```

    `.env` を実行環境の値（DB ホスト・ユーザー・パスワード・データベース名）に書き換えます。
    データベース名を省略した場合の既定値は `cu_ip_limiter`（`DB_NAME`）/ `test_cu_ip_limiter`（`DB_TEST_NAME`）です。

3. テスト用データベース（`Datasources.test` のデータベース）を作成します。

    ```sql
    CREATE DATABASE `test_cu_ip_limiter` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
    ```

4. テストを実行します。スキーマは Migrations（BaserCore ＋ 本プラグイン）から自動構築されます。

    ```shell
    vendor/bin/phpunit
    ```
