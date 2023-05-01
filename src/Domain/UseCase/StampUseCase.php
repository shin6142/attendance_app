<?php

namespace AttendanceApp\Src\Domain\UseCase;

use AttendanceApp\Src\Domain\Model\Stamp;
use AttendanceApp\Src\Domain\Model\Stamps;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
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
}