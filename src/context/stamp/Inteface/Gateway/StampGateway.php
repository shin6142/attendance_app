<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;

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
     * @param Stamp $stamp
     * @return void
     */
    public function save(Stamp $stamp): void;
}