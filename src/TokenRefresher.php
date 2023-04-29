<?php

require_once(__DIR__ . "/../vendor/autoload.php");

class TokenRefresher
{

    public static function run()
    {
        $dotenv = Dotenv\Dotenv::createImmutable("../");
        $dotenv->load();

        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
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

        //POSTデータ 
        $params = http_build_query( 
        array( 
        'grant_type' => 'refresh_token', 
        'client_id' =>  $_ENV['FREEE_CLIENT_ID'], 
        'client_secret' =>  $_ENV['FREEE_CLIENT_SECRET'], 
        'refresh_token' => $data[0]["refresh_token"], //認証用URLで取得したコード 
        'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob')
        ); 
        $headers = array( "Content-Type:application/x-www-form-urlencoded" ); 

        // POSTリクエスト送信 
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, 'https://accounts.secure.freee.co.jp/public_api/token'); 
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST'); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params); 
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

        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
        $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
        die();
        }

        date_default_timezone_set('Asia/Tokyo');
        $stmt = $pdo->prepare("INSERT INTO token (
                access_token, token_type, expires_in, refresh_token, scope, issued_unix_datetime
            ) VALUES (
                :access_token, :token_type, :expires_in, :refresh_token, :scope, :issued_unix_datetime
            )");

        $stmt->bindParam(':access_token', $result["access_token"], PDO::PARAM_STR);
        $stmt->bindParam(':token_type', $result["token_type"], PDO::PARAM_STR);
        $stmt->bindParam(':expires_in', $result["expires_in"], PDO::PARAM_STR);
        $stmt->bindParam(':refresh_token', $result["refresh_token"], PDO::PARAM_STR);
        $stmt->bindParam(':scope', $result["scope"], PDO::PARAM_STR);
        $stmt->bindParam(':issued_unix_datetime', $result["created_at"], PDO::PARAM_STR);
        $res = $stmt->execute();
        if($res){
            echo "DB登録成功しました\n";
        }else{
            echo "DB登録に失敗しました\n";
        }
    }
}