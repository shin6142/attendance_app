<?php

namespace AttendanceApp\Src\Inteface\Controller;

class Request
{
    private function __construct(
        private readonly int $companyId,
        private readonly int $employeeId,
        private readonly string $baseDate,
    ){}

    public static function create(array $request): Request
    {
        $company_id = $request['company_id'];
        $employee_id = $request['employee_id'];
        $base_date = $request['base_date'];
        return new self($company_id, $employee_id, $base_date);
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