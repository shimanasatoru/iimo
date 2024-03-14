<?php 
// Google Clientライブラリを読み込む
require_once 'vendor/autoload.php';

return false;

// Google Cloud Consoleで取得したクライアントIDとクライアントシークレットを設定
$clientID = '547710424482-riltn1tseqnj7p7k7lvi36kigl4dl6qp.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://example.com/callback.php'; // コールバックURLを設定

// Google_Clientインスタンスを作成
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('email');
$client->addScope('profile');

// 認証URLを生成してリダイレクト
$authUrl = $client->createAuthUrl();
header('Location: ' . $authUrl);
exit;

// ?>