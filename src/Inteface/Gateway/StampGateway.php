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
    public function findByDate(int $companyId, int $employeeId, string $date): Stamps;
}