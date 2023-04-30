<?php

namespace AttendanceApp\Src\Domain\Repository;

interface IStampRepository
{
    /**
     * @param int $companyId
     * @param int $employeeId
     * @param string $date
     * @return array
     */
    public function findBy(int $companyId, int $employeeId, string $date): array;
}