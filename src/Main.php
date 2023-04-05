<?php declare(strict_types=1);

require_once(__DIR__ . "/../vendor/autoload.php");


class Main
{

    public static function mainFunc(): int
    {
        return 1;
    }

    public static function record()
    {
        $dotenv = Dotenv\Dotenv::createImmutable("../");
        $dotenv->load();

        //DBから当日の打刻履歴を取得
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
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = :employee_id AND base_date = :base_date ORDER BY type");
        $stmt->bindParam(':employee_id', $_ENV['FREEE_EMPLOYEE_ID'], PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $base_date, PDO::PARAM_STR);
        $res = $stmt->execute();
        $last_record_type = 0;
        if( $res ) {
            $data = $stmt->fetchAll();
            foreach($data as $d){
                if($last_record_type < $d["type"]){
                    $last_record_type = $d["type"];
                }
            }
        }
        if($last_record_type == 4){
            exit;
        }
        $type = $last_record_type + 1;
        $statusArr = [
            0 => "テスト打刻です",
            1 => "開始します。",
            2 => "離席します。",
            3 => "開始します。",
            4 => "終了します。"
        ];
        $dotenv = Dotenv\Dotenv::createImmutable("../");
        $dotenv->load();

        $token = $_ENV['SLACK_TOKEN'];

        //POSTデータ
        $params = http_build_query(
            [
                'channel' => $_ENV['CHANNEL_TIMES_YAMAGA'],
                'text' => $statusArr[$type]
            ]
        );

        $headers = ["Authorization: Bearer $token"];

        //SLACK POSTリクエスト送信
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://slack.com/api/chat.postMessage');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $result = json_decode($body, true);


        // 打刻時間をDBに保存
        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql_host';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
            $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            print('Error:' . $e->getMessage());
            die();
        }

        date_default_timezone_set('Asia/Tokyo');
        $employee_id = $_ENV['FREEE_EMPLOYEE_ID'];
        $company_id = $_ENV['FREEE_COMPANY_ID'];
        $base_date = date('Y-m-d');
        $datetime = date('Y-m-d H:m:s');

        $stmt = $pdo->prepare("INSERT INTO attendance (
                employee_id, company_id, type, base_date, datetime
            ) VALUES (
                :employee_id, :company_id, :type, :base_date, :datetime
            )");

        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_STR);
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $base_date, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);

        $res = $stmt->execute();
    }

    /**
     * @return 
     */
    static public function getAllAttendances(): void
    {
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

        $file = fopen('../data.txt', 'w');
        $stmt = $pdo->prepare("SELECT * FROM attendance");
        $res = $stmt->execute();
        if( $res ) {
            $data = $stmt->fetchAll();
            foreach($data as $d){
                $datetime = new DateTimeImmutable($d["datetime"]);
                fwrite($file, $d["id"] . ' ' . $d["base_date"] . ' ' . $datetime->format('H:i:s') . "\n");
            }
        }
        fclose($file);
    }
}