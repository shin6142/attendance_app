<?php

namespace AttendanceApp\Src\Inteface\Gateway;

use AttendanceApp\Src\Domain\Model\Stamp;

interface StampGateway
{
    /**
     * @param int $companyId
     * @param int $employeeId
     * @param string $date
     * @return array
     */
    public function findBy(int $companyId, int $employeeId, string $date): Stamp;
}