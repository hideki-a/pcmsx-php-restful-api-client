<?php

require_once 'vendor/autoload.php';
require_once 'src/ClientBuilder.php';

use PHPUnit\Framework\TestCase;
use PowerCMSX\RESTfulAPI\ClientBuilder;
use PowerCMSX\RESTfulAPI\HttpMethod;
use PowerCMSX\RESTfulAPI\ContactMethod;

class ClientTest extends TestCase
{
    private $client;
    private static $objectId;
    private static $contactToken;

    protected function setUp(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        $dotenv->load();

        $client = ClientBuilder::create()
            ->setApplicationUrl($_ENV['CMS_API_URL']);
        $this->client = $client;
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }

    public function test_リストの取得(): void
    {
        $response = $this->client->listObject('entry', $_ENV['CMS_WORKSPACE_ID']);
        $this->assertSame(2, $response->totalResult);
    }

    public function test_オブジェクトの取得(): void
    {
        $entry = $this->client->getObject('entry', $_ENV['CMS_WORKSPACE_ID'], (int) $_ENV['TEST_ENTRY_ID']);
        $this->assertSame('サンプル記事です', $entry->title);
    }

    public function test_公開以外のオブジェクトの取得(): void
    {
        $this->client->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);
        $entry = $this->client->getObject('entry', $_ENV['CMS_WORKSPACE_ID'], (int) $_ENV['TEST_DRAFT_ENTRY_ID'], true);
        $this->assertSame('下書き記事です', $entry->title);
    }

    public function test_オブジェクトの作成(): void
    {
        $this->client->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);
        $now = new DateTime();
        $data = [
            'title' => 'APIを用いて登録',
            'text' => 'テスト記事です',
            'basename' => 'test_' . $now->format('ymdhis'),
        ];
        $entry = $this->client->createObject('entry', $_ENV['CMS_WORKSPACE_ID'], $data);
        $this->assertSame('APIを用いて登録', $entry->title);
        self::$objectId = $entry->id;
    }

    #[Depends('test_オブジェクトの作成')]
    public function test_オブジェクトの更新(): void
    {
        $this->client->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);
        $now = new DateTime();
        $data = [
            'text' => $now->format('Y-m-d H:i:s'),
        ];
        $entry = $this->client->updateObject('entry', $_ENV['CMS_WORKSPACE_ID'], self::$objectId, $data);
        $this->assertSame($now->format('Y-m-d H:i:s'), $entry->text);
    }

    #[Depends('test_オブジェクトの作成')]
    #[Depends('test_オブジェクトの更新')]
    public function test_オブジェクトの削除(): void
    {
        $this->client->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);
        $result = $this->client->deleteObject('entry', $_ENV['CMS_WORKSPACE_ID'], self::$objectId);
        $this->assertTrue(property_exists($result, 'Success'));
    }

    public function test_カスタムエンドポイントへのアクセス(): void
    {
        $path = '/0/api_client_test';
        $response = $this->client->runCurl($path, HttpMethod::GET, [], false);
        $this->assertSame('カスタムエンドポイントです', $response->message);
    }

    public function test_問い合わせトークンの取得(): void
    {
        $response = $this->client->contact($_ENV['CMS_WORKSPACE_ID'], $_ENV['TEST_FORM_ID'], ContactMethod::TOKEN);
        $this->assertTrue(property_exists($response, 'magic_token'));
    }

    public function test_問い合わせ投稿内容確認(): void
    {
        $data = [
            'Identifier' => 'apidev_content_feedback',
            'Language' => 'ja',
            'ObjectId' => (int) $_ENV['TEST_ENTRY_ID'],
            'Model' => 'entry',
            'apidev_email' => 'abe@example.com',
            'apidev_feedback' => '疑問が解消できる記事でした。',
        ];
        $response = $this->client->contact(
            $_ENV['CMS_WORKSPACE_ID'],
            $_ENV['TEST_FORM_ID'],
            ContactMethod::CONFIRM,
            $data
        );
        $this->assertTrue(property_exists($response, 'magic_token'));
        self::$contactToken = $response->magic_token;
    }

    public function test_問い合わせ送信(): void
    {
        $data = [
            'Identifier' => 'apidev_content_feedback',
            'Language' => 'ja',
            'ObjectId' => (int) $_ENV['TEST_ENTRY_ID'],
            'Model' => 'entry',
            "MagicToken" => self::$contactToken,
            'apidev_email' => 'abe@example.com',
            'apidev_feedback' => '疑問が解消できる記事でした。',
        ];
        $response = $this->client->contact(
            $_ENV['CMS_WORKSPACE_ID'],
            $_ENV['TEST_FORM_ID'],
            ContactMethod::SUBMIT,
            $data
        );
        $this->assertTrue(property_exists($response, 'Success'));
    }
}
