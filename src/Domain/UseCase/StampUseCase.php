<?php

namespace AttendanceApp\Src\Domain\UseCase;

use AttendanceApp\Src\Domain\Service\DailyStampsService;
use AttendanceApp\Src\Inteface\Database\StampRepository;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use AttendanceApp\Src\Inteface\Logger\Log;
use Dotenv\Dotenv;
use Exception;
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
        $stamps = $this->StampRepository->findBy($company_id, $employee_id, $base_date);
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
    public static function record(int $company_id, int $employee_id, string $date, string $datetime): void
    {
        $repository = new StampRepository();
        $stamps = $repository->findBy($company_id, $employee_id, $date);
        $service = new DailyStampsService();
        $lastStatus = $service->lastStatus($employee_id, $date, $stamps);

        $type = $lastStatus + 1;

        $statusArr = [
            0 => "テスト打刻です",
            1 => "開始します。",
            2 => "離席します。",
            3 => "戻ります。",
            4 => "終了します。"
        ];

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

        $repository->add($company_id, $employee_id, $type, $date, $datetime);

        $array = [
            "employee_id" => $employee_id,
            "company_id" => $company_id,
            "type" => $type,
            "base_date" => $date,
            "datetime" => $datetime,
        ];
        $json = json_encode($array);
        Log::logInfo($json, 'ATTENDANCE');
    }
}