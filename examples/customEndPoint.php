<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__ . DS . '..' . DS . 'src' . DS . 'ClientBuilder.php';

// NOTE: CMS認証情報を.envから読み込み（このサンプルに直接書いて公開することができないので.envを利用しています）
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();

use PowerCMSX\RESTfulAPI\ClientBuilder;
use PowerCMSX\RESTfulAPI\HttpMethod;

$client = ClientBuilder::create()
    ->setApplicationUrl($_ENV['CMS_API_URL']);

$path = '/0/api_client_test'; // /ワークスペースID/プラグインで定義したエンドポイント名
$response = $client->runCurl($path, HttpMethod::GET, [], false);

var_dump($response->message);
