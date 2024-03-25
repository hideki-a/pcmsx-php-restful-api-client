# PowerCMS X RESTful API PHP Client

PHPでPowerCMS XのRESTful APIを素早く・簡単に利用するためのライブラリです。

## 動作条件

- PHP version 8.1 or higher

※Enumを使用しているためで、PHP 8.0.xへの対応は検討中です。（なお、RHEL9等のバックポートを除き、PHP 8.0.xの公式サポートは終了しています。）

## インストール

1. `src`ディレクトリを任意の場所に配置します（ディレクトリ名は適宜変更してください）
2. APIを利用したいファイルにて`require_once`を利用して`src/ClientBuilder.php`を読み込みます

## サンプルコード

`examples`ディレクトリもご覧ください。

### クライアントの準備

```php
require_once 'path' . DS . 'to' . DS . 'ClientBuilder.php';

use PowerCMSX\RESTfulAPI\ClientBuilder;

$client = ClientBuilder::create()
    ->setApplicationUrl('https://powercmsx.localhost/app/api')
    ->setAuthConfig('username', 'password') // 認証を実行する場合のみ設定
    ->setSSLVerification(false) // SSLの検証をスキップする場合のみ設定
    ->setResponseAssociative(true); // レスポンスを連想配列形式で受け取りたい時のみ指定
```

### オブジェクトの取得

```php
$entry = $client->getObject('entry', 0, 1); // 引数: モデル, ワークスペースID, オブジェクトID
var_dump($entry->title); // →記事タイトルが表示されます
```

## メソッド一覧

`powercmsx/docs/README-RESTfulAPI.md`もあわせてご確認ください。

### createObject

オブジェクトを作成します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- data: array リクエストボディ（必須）

### listObjects

オブジェクト一覧を取得します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- data: array リクエストボディ（必須）
- useAuthentication: bool 認証を実行するか否か

### getObject

オブジェクトを取得します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- query: int | string オブジェクトID（またはベースネーム）（必須）
- useAuthentication: bool 認証を実行するか否か
- cols: array 取得するカラム

### updateObject

オブジェクトを更新します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- id: int オブジェクトID（必須）
- data: object リクエストボディ（必須）

### deleteObject

オブジェクトを削除します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- id: int オブジェクトID（必須）

### runCurl

cURL関数を実行します。プラグインで追加したエンドポイントにアクセスする際に使用します。

#### 引数

- path: string パス（必須） ※ワークスペースIDから指定します（例：`/0/api_client_test`）
- method: HttpMethod HTTPメソッド（必須）
- data: array リクエストボディまたはクエリストリングの値（必須）
- useAuthentication: bool 認証を実行するか否か

### search

全文検索を実行します。

#### 引数

- model: string モデル（必須）
- workspaceId: int ワークスペースID（必須）
- data: array 検索パラメータ（必須）

### contact

フォーム投稿を実行します。

#### 引数

- workspaceId: int ワークスペースID（必須）
- id: int オブジェクトID（必須）
- method: ContactMethod コンタクトメソッド（必須）
- data: object リクエストボディ

ContactMethodは以下から選択します。

- トークンの取得: ContactMethod::Token
- 投稿内容の確認: ContactMethod::Confirm
- 投稿内容の送信: ContactMethod::Submit

## 補足

### ステータスの設定

`PowerCMSX\ObjectStatus`を利用するとステータスの値を覚えることなく設定できます。

- `ObjectStatus::Draft`: 下書き
- `ObjectStatus::Review`: レビュー
- `ObjectStatus::ApprovalPending`: 承認待ち
- `ObjectStatus::Reserved`: 公開予約
- `ObjectStatus::Publish`: 公開
- `ObjectStatus::Ended`: 終了

```php
use PowerCMSX\RESTfulAPI\ClientBuilder;
use PowerCMSX\ObjectStatus;

$data = [
    'title' => 'APIのテスト',
    'basename' => 'api_test',
    'status' => ObjectStatus::ApprovalPending->value, // 2が出力され承認待ちになる
];
$entry = $client->createObject('entry', 15, $data);
```

同様に、`PowerCMSX\ObjectEnabled`を利用すると有効・無効の値が取得できます。

- `ObjectEnabled::Disable`: 無効
- `ObjectEnabled::Enable`: 有効
