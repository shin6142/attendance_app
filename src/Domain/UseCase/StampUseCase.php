<?php

namespace AttendanceApp\Src\Domain\UseCase;

use AttendanceApp\Src\Domain\Service\DailyStampsService;
use AttendanceApp\Src\Inteface\Api\SlackApi;
use AttendanceApp\Src\Inteface\Database\StampRepository;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use AttendanceApp\Src\Inteface\Logger\Log;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class StampUseCase
{

    public function __construct(private readonly StampGateway $stampRepository)
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
        $stamps = $this->stampRepository->findBy($company_id, $employee_id, $base_date);
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
    public function record(int $company_id, int $employee_id, string $date, string $datetime): void
    {
        $stamps = $this->stampRepository->findBy($company_id, $employee_id, $date);
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

        $slackApi = new SlackApi();
        $slackApi->sendMessage($statusArr[$type]);

        $this->stampRepository->add($company_id, $employee_id, $type, $date, $datetime);

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