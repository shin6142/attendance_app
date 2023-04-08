<?php

//連携アプリ情報

require_once(__DIR__ . "/../vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql_host';
$user = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASSWORD'];
try {
   $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    print('Error:' . $e->getMessage());
   die();
}
$base_date = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM token ORDER BY issued_unix_datetime DESC LIMIT 1");
$res = $stmt->execute();
$data = false;
if($res){
    echo "DB取得に成功しました\n";
    $data = $stmt->fetchAll();
}else{
    echo "DB取得に失敗しました\n";
}

$access_token = $data[0]['access_token'];

$headers = [ 
    "accept: application/json",
    "Authorization: Bearer $access_token",
    "FREEE-VERSION: 2022-02-01"
]; 

// POSTリクエスト送信 
$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, 'https://api.freee.co.jp/hr/api/v1/users/me'); 
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); 
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_HEADER, true); 

// アクセストークンの取得
$response = curl_exec($curl); 
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
$header = substr($response, 0, $header_size); 
$body = substr($response, $header_size); 
var_dump($body);
$result = json_decode($body, true); 
