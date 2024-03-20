<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__ . DS . '..' . DS . 'src' . DS . 'ClientBuilder.php';

// NOTE: CMS認証情報を.envから読み込み
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();

use PowerCMSX\RESTfulAPI\ClientBuilder;

$client = ClientBuilder::create()
    ->setApplicationUrl($_ENV['CMS_API_URL'])
    ->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);

// 記事の作成
$now = new DateTime();
$data = [
    'title' => 'APIのテスト',
    'text' => $now->format('Y-m-d H:i:s'),
    'basename' => 'test_' . $now->format('ymdhis'),
];
$entry = $client->createObject('entry', 15, $data);

var_dump($entry);

// 記事の削除
$client->deleteObject('entry', 15, $entry->id);
