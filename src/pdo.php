<?php

// refresh freee token
$dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
$user = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASSWORD'];
try{
    $pdo = new PDO($dsn, $user, $password);
    print('接続に成功しました。<br>');
}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$stmt = $pdo->prepare("SELECT * FROM token LIMIT 1");
$res = $stmt->execute();
if( $res ) {
    $data = $stmt->fetch();
}

//POSTデータ 
$params = http_build_query( 
    [
        'grant_type' => 'refresh_token', 
        'client_id' => $_ENV['FREEE_CLIENT_ID'], 
        'client_secret' => $_ENV['FREEE_CLIENT_SECRET'], 
        'refresh_token' => $data['refresh_token']
    ]
); 

$headers = array( "Content-type: application/x-www-form-urlencoded" ); 
    
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

print_r($result); //テスト用に表示

$pdo->exec('TRUNCATE TABLE token');

date_default_timezone_set('Asia/Tokyo');
$access_token = $result['access_token']; 
$token_type = $result['token_type'];
$expires_in = $result['expires_in']; 
$refresh_token = $result['refresh_token']; 
$scope = $result['scope']; 
$issued_unix_datetime = $result['created_at']; 

$stmt = $pdo->prepare("INSERT INTO token (
    access_token, token_type, expires_in, refresh_token, scope, issued_unix_datetime 
) VALUES (
    :access_token, :token_type, :expires_in, :refresh_token, :scope, :issued_unix_datetime
)");

$stmt->bindParam( ':access_token', $access_token, PDO::PARAM_STR);
$stmt->bindParam( ':token_type', $token_type, PDO::PARAM_STR);
$stmt->bindParam( ':expires_in', $expires_in, PDO::PARAM_STR);
$stmt->bindParam( ':refresh_token', $refresh_token, PDO::PARAM_STR);
$stmt->bindParam( ':scope', $scope, PDO::PARAM_STR);
$stmt->bindParam( ':issued_unix_datetime', $issued_unix_datetime, PDO::PARAM_STR);

$res = $stmt->execute();

$pdo = null;


// FREEE API devの場合は、GETリクエストを送り　認証ができていることを確認する
if($mode == 'dev'){
    var_dump("token======" . $access_token);
    // 1. curlの処理を始めるためのコネクションを開く
    $freee_employee_id = $_ENV['FREEE_EMPLOYEE_ID'];
    $get_http_url = 'https://api.freee.co.jp/hr/api/v1/users/me';

    $header = [
        'Authorization: Bearer '.$access_token,  // 前準備で取得したtokenをヘッダに含める
        'Content-Type: application/json',
    ];

    // 2. HTTP通信のRequest-設定情報をSetする
    $get_curl = curl_init();
    curl_setopt($get_curl, CURLOPT_URL, $get_http_url); // url-setting
    curl_setopt($get_curl, CURLOPT_CUSTOMREQUEST, "GET"); // メソッド指定 Ver. GET
    curl_setopt($get_curl, CURLOPT_HTTPHEADER, $header); // HTTP-HeaderをSetting
    curl_setopt($get_curl, CURLOPT_SSL_VERIFYPEER, false); // サーバ証明書の検証は行わない。
    curl_setopt($get_curl, CURLOPT_SSL_VERIFYHOST, false);  
    curl_setopt($get_curl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る

    // 3. curl(HTTP通信)を実行する => レスポンスを変数に入れる
    $response = curl_exec($get_curl);

    // 4. HTTP通信の情報を得る
    $get_http_info = curl_getinfo($get_curl);

    // 5. curlの処理を終了 => コネクションを切断
    curl_close($get_curl);
    var_dump($response);
    $header_size = curl_getinfo($get_curl, CURLINFO_HEADER_SIZE); 
    $header = substr($response, 0, $header_size); 
    $body = substr($response, $header_size);
    $result = json_decode($body, true);
    var_dump($result);
}else if($mode == 'prod'){
    //POSTデータ 
    $url = 'https://api.freee.co.jp/hr/api/v1/employees/' . $_ENV['FREEE_EMPLOYEE_ID'] . '/time_clocks';

    $params = http_build_query( 
        [
            'company_id' => $_ENV['FREEE_COMPANY_ID'], 
            'type' => $type, 
            'base_date' => date('Y-m-d'), 
            'datetime' => date('Y-m-d H:m:s'),
        ]
    ); 

    $header = [
        'Authorization: Bearer '.$access_token,  // 前準備で取得したtokenをヘッダに含める
        'Content-Type: application/json',
    ];
    print_r($access_token);
    //POSTリクエスト送信 
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params); 
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($curl, CURLOPT_HEADER, true); 
        
    //アクセストークンの取得
    $response = curl_exec($curl); 
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE); 
    $header = substr($response, 0, $header_size); 
    $body = substr($response, $header_size); 
    $result = json_decode($body, true); 

    print_r($result); //テスト用に表示
}