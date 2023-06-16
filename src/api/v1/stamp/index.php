<?php

require_once(__DIR__ . "/Main.php");

// 処理実行前の値
$result['success'] = false;
$result["error"] = '';

try{
    // パラメータを受け取る
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];
    $base_date = $_REQUEST['base_date'];

    // 必要なパラメータがない場合、エラーを返す
    if(!isset($company_id)){
        throw new Exception('会社IDを指定してください');
    }
    if(!isset($employee_id)){
        throw new Exception('従業員IDを指定してください');
    }
    if(!isset($base_date)){
        throw new Exception('打刻日を指定してください');
    }

    // envファイルのロード
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
    $dotenv->load();

    // DB接続
    $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
    $user = $_ENV['MYSQL_USER'];
    $password = $_ENV['MYSQL_PASSWORD'];
    try {
        $pdo = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        throw new Exception($e->getMessage());
    }

    // SQL実行
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE company_id = :company_id AND employee_id = :employee_id AND base_date = :base_date ORDER BY type");
    $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_STR);
    $stmt->bindParam(':base_date', $base_date, PDO::PARAM_STR);
    $res = $stmt->execute();

    $data = [];
    $resultArr = [];

  // SQL結果をdataに代入
    if ($res) {
        $data = $stmt->fetchAll();
    }

  // 0番目の要素を取り出す
    $resultArr['employee_id'] = $data[0]['employee_id'];
    $resultArr['company_id'] = $data[0]['company_id'];
    $resultArr['base_date'] = $data[0]['base_date'];

  // typeの判定
    foreach ($data as $d) {
        $type = $d['type'];
        switch ($type) {
            // 開始
            case 1:
                $resultArr['start_datetime'] = $d['datetime'];
                break;
            // 離脱
            case 2:
                $resultArr['leave_datetime'] = $d['datetime'];
                break;
            // 戻ってくる
            case 3:
                $resultArr['back_datetime'] = $d['datetime'];
                break;
            // 終了
            case 4:
                $resultArr['end_datetime'] = $d['datetime'];
        }
    }

    $result["content"] = $resultArr;
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
}

catch(Exception $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;