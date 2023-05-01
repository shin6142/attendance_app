<?php

namespace AttendanceApp\Src\Inteface\Controller;

class Request
{

    private readonly int $companyId;
    private readonly int $employeeId;
    private readonly string $baseDate;

    public function __construct(int $companyId, int $employeeId, string $baseDate)
    {
        $this->companyId = $companyId;
        $this->employeeId = $employeeId;
        $this->baseDate = $baseDate;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getBaseDate(): string
    {
        return $this->baseDate;
    }

}