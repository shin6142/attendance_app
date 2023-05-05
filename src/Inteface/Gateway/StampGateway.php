<?php

namespace AttendanceApp\Src\Inteface\Gateway;

use AttendanceApp\Src\Domain\Model\Stamps;

interface StampGateway
{
    /**
     * @param int $companyId
     * @param int $employeeId
     * @param string $date
     * @return Stamps
     */
    public function findBy(int $companyId, int $employeeId, string $date): Stamps;

    /**
     * @param int $companyId
     * @param int $employeeId
     * @param int $type
     * @param string $date
     * @param string $datetime
     * @return void
     */
    public function add(int $companyId, int $employeeId, int $type, string $date, string $datetime): void;
}