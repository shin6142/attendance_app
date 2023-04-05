<?php 
//連携アプリ情報
define('APP_ID', '581d602e60ecf265b20c1f3f9046f12efb7c218d33fae8d7198eea16e6fca12b');
define('SECRET', '49db8878bbddadea5557f08e6e73cd1f74d600173ed6f1ecb36c7352373ef1b3');
define('CALLBACK_URL', 'https://app.secure.freee.co.jp/developers/start_guides/applications/22881/token?company_id=10639254'); 

//POSTデータ 
$params = http_build_query( 
array( 
'grant_type' => 'authorization_code', 
'client_id' => APP_ID, 
'client_secret' => SECRET, 
'code' => $_GET['code'], //認証用URLで取得したコード 
'redirect_uri' => CALLBACK_URL, )); 
$headers = array( "Content-type: application/json" ); 

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
$result = json_decode($body, true); 

$access_token = $result['access_token']; 
print_r($access_token); //テスト用に表示