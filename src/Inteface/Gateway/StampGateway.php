<?php

namespace AttendanceApp\Src\Inteface\Gateway;

interface StampGateway
{
    /**
     * @param int $companyId
     * @param int $employeeId
     * @param string $date
     * @return array
     */
    public function findBy(int $companyId, int $employeeId, string $date): array;
}