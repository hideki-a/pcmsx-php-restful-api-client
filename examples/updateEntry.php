<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__ . DS . '..' . DS . 'src' . DS . 'ClientBuilder.php';

// NOTE: CMS認証情報を.envから読み込み
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . DS . '..' . DS)->load();

use PowerCMSX\RESTfulAPI\ClientBuilder;

$client = ClientBuilder::create()
    ->setApplicationUrl($_ENV['CMS_API_URL'])
    ->setAuthConfig($_ENV['CMS_USER_NAME'], $_ENV['CMS_PASSWORD']);

$now = new DateTime();
$data = [
    'text' => $now->format('Y-m-d H:i:s'),
];
$entry = $client->updateObject('entry', 0, 15000, $data);

var_dump($entry->text);
