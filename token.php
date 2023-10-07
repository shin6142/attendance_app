<?php 

require_once(__DIR__ . "/vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();

//POSTデータ 
$params = http_build_query([
    'grant_type' => 'authorization_code', 
    'client_id' =>  $_ENV['FREEE_CLIENT_ID'], 
    'client_secret' =>  $_ENV['FREEE_CLIENT_SECRET'], 
    'code' => '', //認証用URLで取得したコード 
    'redirect_uri' => $_ENV['FREEE_CALLBACK_URI']
]); 
$headers = array( "Content-Type:application/x-www-form-urlencoded" ); 

//POSTリクエスト送信 
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, 'https://accounts.secure.freee.co.jp/public_api/token'); 
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
curl_setopt($curl, CURLOPT_POSTFIELDS, $params); 
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_HEADER, true); 

//アクセストークンの取得
$response = curl_exec($curl); 
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
$header = substr($response, 0, $header_size); 
$body = substr($response, $header_size); 
var_dump($body);
$result = json_decode($body, true); 