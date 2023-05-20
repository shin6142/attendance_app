<?php

namespace AttendanceApp\Src\Context\stamp\Domain\UseCase;

use AttendanceApp\Src\Context\stamp\Domain\Model\ClockInHistory;
use AttendanceApp\Src\Context\stamp\Domain\Model\Employee;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\FreeeApiGateway;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\SlackAPIGateway;
use AttendanceApp\Src\Context\stamp\Inteface\Gateway\StampGateway;
use Exception;

class StampUseCase
{

    public function __construct(
        private readonly StampGateway       $stampRepository,
        private readonly SlackAPIGateway    $slackAPIGateway,
        private readonly FreeeApiGateway    $freeeApiGateway
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
        $history = ClockInHistory::create($employee_id, $company_id, $stamps);
        return new DailyStampsDto(
            $employee_id,
            $company_id,
            $base_date,
            $history->getByType(1)?->getDateTime(),
            $history->getByType(2)?->getDateTime(),
            $history->getByType(3)?->getDateTime(),
            $history->getByType(4)?->getDateTime(),
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
        $stampsFiltered = $stamps->filterByDate($date)->filterByEmployeeId($employee_id);

        $history = ClockInHistory::create($employee_id, $company_id, $stampsFiltered);
        $employee = new Employee(1);
        $stamp = $employee->clockIn($company_id, $date, $datetime, $history);
        $this->stampRepository->save($stamp);
        $type = $stamp->getType();
        $this->slackAPIGateway->send($type);

        if ($type === 4) {
            $this->recordAttendanceOnFreee($company_id, $employee_id, $date);
        }
    }

    /**
     * @throws Exception
     */
    private function recordAttendanceOnFreee(int $company_id, int $employee_id, string $date): void
    {
        $stamps = $this->stampRepository->findBy($company_id, $employee_id, $date);
        $filtered = $stamps->filterByDate($date)->filterByEmployeeId($employee_id);
        $history = ClockInHistory::create($employee_id, $company_id, $filtered);
        $dto = new DailyStampsDto(
            $employee_id,
            $company_id,
            $date,
            $history->getByType(1)?->getDateTime(),
            $history->getByType(2)?->getDateTime(),
            $history->getByType(3)?->getDateTime(),
            $history->getByType(4)?->getDateTime(),
        );
        $this->freeeApiGateway->registerAttendance($dto);
    }
}