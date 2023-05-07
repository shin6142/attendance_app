<?php

namespace AttendanceApp\Src\Context\stamp\Domain\UseCase;

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Service\DailyStampsService;
use AttendanceApp\Src\Context\stamp\Inteface\Api\FreeeApi;
use AttendanceApp\Src\Context\stamp\Inteface\Api\SlackApi;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\StampGateway;
use AttendanceApp\Src\Context\stamp\Inteface\Logger\Log;
use Exception;

class StampUseCase
{

    public function __construct(
        private readonly StampGateway       $stampRepository,
        private readonly DailyStampsService $dailyStampsService
    )
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

        return new DailyStampsDto(
            $employee_id,
            $company_id,
            $base_date,
            $this->dailyStampsService->getByType($employee_id, $base_date, $stamps, 1)?->getDateTime(),
            $this->dailyStampsService->getByType($employee_id, $base_date, $stamps, 2)?->getDateTime(),
            $this->dailyStampsService->getByType($employee_id, $base_date, $stamps, 3)?->getDateTime(),
            $this->dailyStampsService->getByType($employee_id, $base_date, $stamps, 4)?->getDateTime(),
        );
    }

    /**
     * @param int $company_id
     * @param int $employee_id
     * @param string $date
     * @param string $datetime
     * @return void
     * @throws Exception
     */
    public function record(int $company_id, int $employee_id, string $date, string $datetime): void
    {
        $stamps = $this->stampRepository->findBy($company_id, $employee_id, $date);
        $lastStatus = $this->dailyStampsService->lastStatus($employee_id, $date, $stamps);
        $type = $lastStatus + 1;
        $stamp = Stamp::create($company_id, $employee_id, $type, $date, $datetime);
        $this->stampRepository->save($stamp);

        $slackApi = new SlackApi();
        $slackApi->sendMessage($type);

        $array = [
            "employee_id" => $employee_id,
            "company_id" => $company_id,
            "type" => $type,
            "base_date" => $date,
            "datetime" => $datetime,
        ];
        $json = json_encode($array);
        Log::logInfo($json, 'ATTENDANCE');

        if($type === 4){
            $this->recordAttendanceOnFreee($company_id, $employee_id, $date);
        }
    }

    /**
     * @throws Exception
     */
    private function recordAttendanceOnFreee(int $company_id, int $employee_id, string $base_date): void
    {
        $dto = $this->getByDate($company_id, $employee_id, $base_date);
        $freeeApi = new FreeeApi();
        $freeeApi->registerAttendance($dto);
    }
}