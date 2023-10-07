<?php

namespace AttendanceApp\Src\Context\stamp\Domain\UseCase;

class DailyStampsDto
{
    public function __construct(
        private readonly int $employeeId,
        private readonly int $companyId,
        private readonly string $date,
        private readonly string|null $startDatetime,
        private readonly string|null $leaveDatetime,
        private readonly string|null $backDatetime,
        private readonly string|null $endDatetime,
    ){}

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getStartDatetime(): ?string
    {
        return $this->startDatetime;
    }

    /**
     * @return string|null
     */
    public function getLeaveDatetime(): ?string
    {
        return $this->leaveDatetime;
    }

    /**
     * @return string|null
     */
    public function getBackDatetime(): ?string
    {
        return $this->backDatetime;
    }

    /**
     * @return string|null
     */
    public function getEndDatetime(): ?string
    {
        return $this->endDatetime;
    }
}