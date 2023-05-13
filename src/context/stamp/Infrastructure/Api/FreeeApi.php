<?php

namespace AttendanceApp\Src\Context\stamp\Infrastructure\Api;

use AttendanceApp\Src\Context\stamp\Domain\UseCase\DailyStampsDto;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\FreeeApiGateway;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class FreeeApi implements FreeeApiGateway
{

    public function __construct()
    {
        $this->token = '';
    }

    public function getEmployeeInfo(): void
    {
        // TODO: Implement getEmployeeInfo() method.
    }

    public function registerAttendance(DailyStampsDto $dto): void
    {
        $this->refreshToken();
        $token = $this->findLatestToken();
        $access_token = $token['access_token'];

        $curl = curl_init();

        $companyId = $dto->getCompanyId();
        $employeeId = $dto->getEmployeeId();
        $date = $dto->getDate();

        $leaveDateTime = $dto->getLeaveDatetime();
        $backDateTime = $dto->getBackDatetime();
        $startDatetime = $dto->getStartDatetime();
        $endDateTime = $dto->getEndDatetime();

        $url = "https://api.freee.co.jp/hr/api/v1/employees/$employeeId/work_records/$date";
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $access_token,
            "Content-Type: application/json",
            "FREEE-VERSION: 2022-02-01"
        );

        $data = array(
            "company_id" => $companyId,
            "break_records" => array(
                array(
                    "clock_in_at" => $leaveDateTime,
                    "clock_out_at" => $backDateTime
                )
            ),
            "clock_in_at" => $startDatetime,
            "clock_out_at" => $endDateTime
        );

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($curl);

        if ($result === false) {
            throw new Exception(curl_error($curl));
        }

        curl_close($curl);
    }

    private function findLatestToken(): array
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../../../");
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
        $stmt = $pdo->prepare("SELECT * FROM token ORDER BY issued_unix_datetime DESC LIMIT 1");
        $res = $stmt->execute();
        $data = false;
        if ($res) {
            echo "DB取得に成功しました\n";
            $data = $stmt->fetchAll();
        } else {
            echo "DB取得に失敗しました\n";
        }

        return $data[0];
    }

    private function refreshToken(): void
    {
        $token = $this->findLatestToken();
        //POSTデータ
        $params = http_build_query(
            [
                'grant_type' => 'refresh_token',
                'client_id' => $_ENV['FREEE_CLIENT_ID'],
                'client_secret' => $_ENV['FREEE_CLIENT_SECRET'],
                'refresh_token' => $token['refresh_token'], //認証用URLで取得したコード
                'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob'
            ]
        );
        $headers = ["Content-Type:application/x-www-form-urlencoded"];

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
        if ($res) {
            echo "DB登録成功しました\n";
        } else {
            echo "DB登録に失敗しました\n";
        }
    }
}