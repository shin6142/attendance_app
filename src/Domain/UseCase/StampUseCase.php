<?php

namespace AttendanceApp\Src\Domain\UseCase;

use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use Dotenv\Dotenv;
use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PDO;
use PDOException;

class StampUseCase
{

    public function __construct(private readonly StampGateway $StampRepository)
    {
    }

    /**
     * @param int $company_id
     * @param int $employee_id
     * @param string $base_date
     * @return DailyStampsDto
     */
    public function getByDate(int $company_id, int $employee_id, string $base_date): DailyStampsDto
    {
        $stamps = $this->StampRepository->findByDate($company_id, $employee_id, $base_date);
        $stampArray = $stamps->getStamps();
        $companyId = $stampArray[0]->getCompanyId();
        $employeeId = $stampArray[0]->getEmployeeId();
        $date = $stampArray[0]->getDate();
        $startDateTime = null;
        $leaveDateTime = null;
        $backDateTime = null;
        $endDateTime = null;

        foreach($stampArray as $s){
            $type = $s->getType();
            switch ($type){
                case 1:
                    $startDateTime = $s->getDateTime();
                    break;
                case 2:
                    $leaveDateTime = $s->getDateTime();
                    break;
                case 3:
                    $backDateTime = $s->getDateTime();
                    break;
                case 4:
                    $endDateTime = $s->getDateTime();
            }
        }
        return new DailyStampsDto(
            $employeeId,
            $companyId,
            $date,
            $startDateTime,
            $leaveDateTime,
            $backDateTime,
            $endDateTime,
        );
    }

    /**
     * @throws Exception
     */
    public static function record(int $company_id, int $employee_id)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();

        //DBから当日の打刻履歴を取得
        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
            $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        $base_date = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE company_id = :company_id AND employee_id = :employee_id AND base_date = :base_date ORDER BY type");
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $base_date, PDO::PARAM_STR);
        $res = $stmt->execute();
        if (!$res) {
            throw new Exception('当日の打刻情報の取得に失敗しました');
        }

        $last_record_type = 0;
        $data = $stmt->fetchAll();
        foreach ($data as $d) {
            if ($last_record_type < $d["type"]) {
                $last_record_type = $d["type"];
            }
        }

        if ($last_record_type == 4) {
            exit;
        }
        $type = $last_record_type + 1;

        $statusArr = [
            0 => "テスト打刻です",
            1 => "開始します。",
            2 => "離席します。",
            3 => "戻ります。",
            4 => "終了します。"
        ];

        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../../");
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

        // SLACK POSTリクエスト送信
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
        if (!$result["ok"]) {
            throw new Exception('スラック投稿に失敗しました');
        }

        // 打刻時間をDBに保存
        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
            $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        date_default_timezone_set('Asia/Tokyo');
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
        if (!$res) {
            throw new Exception('打刻情報のDB登録に失敗しました');
        }

        $array = [
            "employee_id" => $employee_id,
            "company_id" => $company_id,
            "type" => $type,
            "base_date" => $base_date,
            "datetime" => $datetime,
        ];
        $json = json_encode($array);

        // タイムゾーン設定
        date_default_timezone_set("Asia/Tokyo");
        // フォーマッタの作成
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel% %level_name% %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        // ハンドラの作成
        $stream = new StreamHandler(__DIR__ . '/../../../logs/record.log', Level::Info); // ログレベルINFO以上のみ出力
        $stream->setFormatter($formatter);

        // ロガーオブジェクトの作成
        $logger = new Logger('ATTENDANCE');
        $logger->pushHandler($stream);
        $logger->info($json); // 出力される
    }
}