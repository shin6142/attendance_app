<?php

namespace AttendanceApp\Src\Inteface\Controller;

use AttendanceApp\Src\Domain\UseCase\DailyStampsDto;
use AttendanceApp\Src\Domain\UseCase\StampUseCase;
use PhpParser\Node\Expr\Array_;

class StampController
{
    public function __construct(private readonly StampUseCase $useCase){}

    public function getStampsByDate(int $companyId, int $employeeId, string $date): Array
    {
        $dto = $this->useCase->getByDate($companyId, $employeeId, $date);
        $result['employee_id'] = $dto->getEmployeeId();
        $result['company_id'] = $dto->getCompanyId();
        $result['base_date'] = $dto->getDate();
        $result['start_datetime'] = $dto->getStartDatetime();
        $result['leave_datetime'] = $dto->getLeaveDatetime();
        $result['back_datetime'] = $dto->getBackDatetime();
        $result['end_datetime'] = $dto->getEndDatetime();

        return $result;
    }
}