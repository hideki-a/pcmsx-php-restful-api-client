<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

require_once 'enums' . DIRECTORY_SEPARATOR . 'ApiMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ContactMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'HttpMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ObjectStatus.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ObjectEnabled.php';

use stdClass;

class Client
{
    public const VERSION = '1.0.1';

    private $applicationUrl;
    private $apiVersion = 1;
    private $sslVerification = true;
    private $responseAssociative = false;
    private $userName;
    private $password;
    private $token;

    /**
     * アプリケーションURLの設定
     *
     * @param string $url アプリケーションURL
     */
    public function setApplicationUrl(string $url): Client
    {
        $this->applicationUrl = $url;

        return $this;
    }

    /**
     * 認証情報の設定
     *
     * @param string $userName ユーザー名
     * @param string $password パスワード
     */
    public function setAuthConfig(string $userName, string $password): Client
    {
        $this->userName = $userName;
        $this->password = $password;

        return $this;
    }

    /**
     * cURL関数のSSL検証設定
     *
     * @param bool $value
     */
    public function setSSLVerification(bool $value = true): Client
    {
        $this->sslVerification = $value;
        return $this;
    }

    /**
     * レスポンス形式設定
     *
     * `true`の場合、連想配列形式になります。
     *
     * @param bool $value
     */
    public function setResponseAssociative(bool $value = false): Client
    {
        $this->responseAssociative = $value;
        return $this;
    }

    /**
     * cURL関数の実行
     *
     * @param string $path パス
     * @param HttpMethod $method HTTPメソッド
     * @param array $data リクエストボディまたはクエリストリングの値
     * @param bool $useAuthentication 認証を利用するか否か
     */
    public function runCurl(
        string $path,
        HttpMethod $method,
        array $data,
        bool $useAuthentication = false
    ): stdClass|array {
        if (!$this->applicationUrl) {
            throw new \Exception('Application URL must be required.');
        }

        $headers = [
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerification);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method !== HttpMethod::GET) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            if ($method === HttpMethod::POST) {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method->name);
            }
        }

        if ($useAuthentication) {
            $headers[] = "X-PCMSX-Authorization: {$this->token->access_token}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $queryString = '';
        if ($method === HttpMethod::GET) {
            $queryString = count($data) ? '?' . http_build_query($data) : '';
        }
        curl_setopt($ch, CURLOPT_URL, "{$this->applicationUrl}/v{$this->apiVersion}{$path}{$queryString}");

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, $this->responseAssociative);
    }

    private function authentication(): void
    {
        $data = [
            'name' => $this->userName,
            'password' => $this->password,
        ];
        $response = $this->runCurl('/authentication', HttpMethod::POST, $data);

        if (property_exists($response, 'access_token')) {
            $this->token = $response;
        } else {
            throw new \Exception($response->message);
        }
    }

    private function buildPath(int $workspaceId, string $model, ApiMethod $apiMethod, ?int $objectId): string
    {
        $path = "/{$workspaceId}/{$model}/" . strtolower($apiMethod->name);

        if (in_array($apiMethod, [ApiMethod::Get, ApiMethod::Update, ApiMethod::Delete])) {
            if ($objectId) {
                $path .= "/{$objectId}";
            }
        }

        return $path;
    }

    private function request(
        string $model,
        int $workspaceId,
        ApiMethod $apiMethod,
        array $data,
        bool $useAuthentication = false,
        int $objectId = null
    ): stdClass|array {
        $httpMethod = HttpMethod::POST;

        if ($apiMethod === ApiMethod::List || $apiMethod === ApiMethod::Get) {
            $httpMethod = HttpMethod::GET;
        }

        $path = $this->buildPath($workspaceId, $model, $apiMethod, $objectId);

        if ($useAuthentication) {
            if (!$this->token || $this->token->expires_in < time()) {
                $this->authentication();
            }
        }

        return $this->runCurl($path, $httpMethod, $data, $useAuthentication);
    }

    /**
     * オブジェクトリストの取得
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param array $data パラメータ
     * @param bool $useAuthentication 認証を利用するか否か
     */
    public function listObject(
        string $model,
        int $workspaceId,
        array $data = [],
        bool $useAuthentication = false
    ): stdClass|array {
        return $this->request($model, $workspaceId, ApiMethod::List, $data, $useAuthentication);
    }

    /**
     * 単一オブジェクトの取得
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param int|string $query オブジェクトIDもしくはベースネーム
     * @param bool $useAuthentication 認証を利用するか否か
     * @param array $cols 取得するカラム
     */
    public function getObject(
        string $model,
        int $workspaceId,
        int|string $query,
        bool $useAuthentication = false,
        array $cols = []
    ): stdClass|array {
        $data = [];

        if (is_int($query)) {
            $objectId = $query;
        } elseif (is_string($query)) {
            $data['basename'] = $query;
        }

        if (count($cols)) {
            $data['cols'] = implode(',', $cols);
        }

        return $this->request($model, $workspaceId, ApiMethod::Get, $data, $useAuthentication, $objectId);
    }

    /**
     * オブジェクトの作成
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param array $data リクエストボディ
     */
    public function createObject(string $model, int $workspaceId, array $data): stdClass|array
    {
        return $this->request($model, $workspaceId, ApiMethod::Insert, $data, true);
    }

    /**
     * オブジェクトの更新
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param int $objectId オブジェクトID
     * @param array $data リクエストボディ
     */
    public function updateObject(string $model, int $workspaceId, int $objectId, array $data): stdClass|array
    {
        return $this->request($model, $workspaceId, ApiMethod::Update, $data, true, $objectId);
    }

    /**
     * オブジェクトの削除
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param int $objectId オブジェクトID
     */
    public function deleteObject(string $model, int $workspaceId, int $objectId): stdClass|array
    {
        return $this->request($model, $workspaceId, ApiMethod::Delete, [], true, $objectId);
    }

    /**
     * 全文検索
     *
     * @param string $model モデル名
     * @param int $workspaceId ワークスペースID
     * @param array $data 検索パラメータ
     */
    public function search(string $model, int $workspaceId, array $data): stdClass|array {
        $path = "/{$workspaceId}/{$model}/search";
        return $this->runCurl($path, HttpMethod::GET, $data);
    }

    /**
     * フォーム投稿
     *
     * @param int $workspaceId ワークスペースID
     * @param int $objectId オブジェクトID
     * @param ContactMethod $method メソッド名
     * @param array $data リクエストボディ
     */
    public function contact(
        int $workspaceId,
        int $formId,
        ContactMethod $method,
        array $data = []
    ): stdClass|array {
        $path = "/{$workspaceId}/contact/" . strtolower($method->name) . "/${formId}";
        return $this->runCurl($path, HttpMethod::POST, $data);
    }
}
