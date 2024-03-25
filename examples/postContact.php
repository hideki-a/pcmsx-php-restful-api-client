<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__ . DS . '..' . DS . 'src' . DS . 'ClientBuilder.php';

// NOTE: CMS認証情報を.envから読み込み（このサンプルに直接書いて公開することができないので.envを利用しています）
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . DS . '..' . DS)->load();

use PowerCMSX\RESTfulAPI\ClientBuilder;
use PowerCMSX\RESTfulAPI\ContactMethod;

$client = ClientBuilder::create()
    ->setApplicationUrl($_ENV['CMS_API_URL']);

$data = [
    'Identifier' => 'apidev_content_feedback',
    'Language' => 'ja',
    'ObjectId' => (int) $_ENV['TEST_ENTRY_ID'],
    'Model' => 'entry',
    'apidev_email' => 'abe@example.com',
    'apidev_feedback' => '疑問が解消できる記事でした。',
];
$response = $client->contact($_ENV['CMS_WORKSPACE_ID'], $_ENV['TEST_FORM_ID'], ContactMethod::Confirm, $data);

if (property_exists($response, 'magic_token')) {
    $token = $response->magic_token;
    $data['MagicToken'] = $token;
    $response = $client->contact($_ENV['CMS_WORKSPACE_ID'], $_ENV['TEST_FORM_ID'], ContactMethod::Submit, $data);
}
