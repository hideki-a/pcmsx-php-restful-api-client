<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__ . DS . '..' . DS . 'src' . DS . 'ClientBuilder.php';

// NOTE: CMS認証情報を.envから読み込み（このサンプルに直接書いて公開することができないので.envを利用しています）
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . DS . '..' . DS)->load();

use PowerCMSX\RESTfulAPI\ClientBuilder;

$client = ClientBuilder::create()
    ->setApplicationUrl($_ENV['CMS_API_URL']);
$entry = $client->getObject('entry', 4, 15078);

var_dump($entry->title);
