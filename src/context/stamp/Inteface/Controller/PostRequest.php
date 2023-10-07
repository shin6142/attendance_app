<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Controller;

class PostRequest
{
    public function __construct(
        private readonly int $companyId,
        private readonly int $employeeId,
        private readonly string $date,
        private readonly string $datetime
    )
    {
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDatetime(): string
    {
        return $this->datetime;
    }

}