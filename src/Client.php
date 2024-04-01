<?php

declare(strict_types=1);

namespace PowerCMSX\RESTfulAPI;

require_once 'enums' . DIRECTORY_SEPARATOR . 'ApiMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ContactMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'HttpMethod.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ObjectStatus.php';
require_once 'enums' . DIRECTORY_SEPARATOR . 'ObjectEnabled.php';

use stdClass;
use Exception;

class Client
{
    public const VERSION = '1.1.1';

    private string $applicationUrl;
    private int $apiVersion = 1;
    private bool $sslVerification = true;
    private bool $responseAssociative = false;
    private string $userName;
    private string $password;
    private ?stdClass $token = null;
    private ?array $lastCurlResponseHeaders = null;
    private bool $useCookie = false;
    private ?stdClass $cookie = null;

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
     * Cookie設定
     *
     * `true`の場合、Cookie(pt-api-user)が利用できます。
     *
     * @param bool $value
     */
    public function setUseCookie(bool $value = false): Client
    {
        $this->useCookie = $value;
        return $this;
    }

    /**
     * 最後に実行したcURL関数のレスポンスヘッダーの取得
     *
     * @return array レスポンスヘッダー
     */
    public function getLastCurlResponseHeader(): ?array
    {
        return $this->lastCurlResponseHeaders;
    }

    /**
     * Cookie(pt-api-user)の取得
     *
     * @return stdClass Cookieデータ
     */
    public function getCookie(): ?stdClass
    {
        return $this->cookie;
    }

    private function setCookie(array $responseHeaders): void
    {
        foreach ($responseHeaders as $key => $value) {
            if ($key === 'Set-Cookie' && strpos($value, 'pt-api-user') === 0) {
                preg_match('/^pt-api-user=([^;]+); expires=(.*?);.*$/', $value, $matches);
                $this->cookie = (object) [
                    'value' => $matches[1],
                    'expires' => $matches[2],
                ];
            }
        }
    }

    private function makeRequestHeaders(bool $useAuthentication = false): array
    {
        $headers = [
            'Content-Type: application/json',
        ];

        if ($useAuthentication) {
            if (!$this->token || $this->token->expires_in < time()) {
                $this->authentication();
            }

            $token = $this->token ? $this->token->access_token : '';
            $headers[] = "X-PCMSX-Authorization: {$token}";
        }

        return $headers;
    }

    private function setCurlResponseHeaderOption(\CurlHandle &$ch, array &$responseHeaders): void
    {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function (\CurlHandle $ch, string $header) use (&$responseHeaders) {
            $data = explode(': ', $header);
            if (count($data) >= 2) {
                $responseHeaders[trim($data[0])] = trim($data[1]);
            }

            return strlen($header);
        });
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
            exit('Application URL must be required.');
        }

        $ch = curl_init();
        if (!$ch) {  /** @phpstan-ignore-line */
            exit('Could not initialize cURL session.');
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerification);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // メソッド・送信データのセット
        if ($method !== HttpMethod::GET) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            if ($method === HttpMethod::POST) {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method->name);
            }
        }

        // リクエストヘッダー
        $headers = $this->makeRequestHeaders($useAuthentication);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // レスポンスヘッダー
        $responseHeaders = [];
        $this->setCurlResponseHeaderOption($ch, $responseHeaders);

        // クエリストリングの作成とURLの設定
        $queryString = '';
        if ($method === HttpMethod::GET) {
            $queryString = count($data) ? '?' . http_build_query($data) : '';
        }
        curl_setopt($ch, CURLOPT_URL, "{$this->applicationUrl}/v{$this->apiVersion}{$path}{$queryString}");

        // cURL実行
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseBody = substr($response, $headerSize);
        $this->lastCurlResponseHeaders = $responseHeaders;
        if (!@json_decode($responseBody)) {
            if ($responseBody) {
                throw new Exception($responseBody);
            }
            throw new Exception('cURL session execution failed.');
        }

        // Cookie取得
        if ($this->useCookie) {
            $this->setCookie($responseHeaders);
        }

        curl_close($ch);

        return json_decode($responseBody, $this->responseAssociative);
    }

    private function authentication(): void
    {
        $data = [
            'name' => $this->userName,
            'password' => $this->password,
        ];
        $response = $this->runCurl('/authentication', HttpMethod::POST, $data);
        $response = (object) $response;

        if (property_exists($response, 'access_token')) {
            $this->token = $response;
        } else {
            exit($response->message);
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
        $objectId = null;

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
    public function search(string $model, int $workspaceId, array $data): stdClass|array
    {
        $path = "/{$workspaceId}/{$model}/search";
        return $this->runCurl($path, HttpMethod::GET, $data);
    }

    /**
     * フォーム投稿
     *
     * @param int $workspaceId ワークスペースID
     * @param int $formId フォームID
     * @param ContactMethod $method メソッド名
     * @param array $data リクエストボディ
     */
    public function contact(
        int $workspaceId,
        int $formId,
        ContactMethod $method,
        array $data = []
    ): stdClass|array {
        $path = "/{$workspaceId}/contact/" . strtolower($method->name) . "/{$formId}";
        return $this->runCurl($path, HttpMethod::POST, $data);
    }

    /**
     * カスタムエンドポイントへのリクエスト
     *
     * @param string $endpointName エンドポイント名
     * @param int $workspaceId ワークスペースID
     * @param HttpMethod $method HTTPメソッド
     * @param array $data リクエストボディまたはクエリストリングの値
     * @param bool $useAuthentication 認証を利用するか否か
     */
    public function requestCustomEndpoint(
        string $endpointName,
        int $workspaceId,
        HttpMethod $method,
        array $data = [],
        bool $useAuthentication = false,
    ): stdClass|array {
        $path = "/{$workspaceId}/{$endpointName}";
        return $this->runCurl($path, $method, $data, $useAuthentication);
    }
}
