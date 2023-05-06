<?php

namespace AttendanceApp\Src\Inteface\Controller;

use AttendanceApp\Src\Domain\UseCase\StampUseCase;

class StampController
{
    public function __construct(private readonly StampUseCase $useCase){}

    public function getStampsByDate(GetRequest $request): array
    {
        $dto = $this->useCase->getByDate(
            $request->getCompanyId(),
            $request->getEmployeeId(),
            $request->getBaseDate()
        );
        $result['employee_id'] = $dto->getEmployeeId();
        $result['company_id'] = $dto->getCompanyId();
        $result['base_date'] = $dto->getDate();
        $result['start_datetime'] = $dto->getStartDatetime();
        $result['leave_datetime'] = $dto->getLeaveDatetime();
        $result['back_datetime'] = $dto->getBackDatetime();
        $result['end_datetime'] = $dto->getEndDatetime();

        return $result;
    }

    public function record(PostRequest $request): void{
        $companyId = 1;
        $employeeId = 1;
        $date = '2023-06-01';
        $datetime = '2023-06-01 12:00:00';
        $this->useCase->record($companyId, $employeeId, $date, $datetime);
    }
}