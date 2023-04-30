<?php

namespace AttendanceApp\Src\Domain\Model;


class Stamp
{
    private function __construct(
        private readonly string $id,
        private readonly int    $companyId,
        private readonly int    $employeeId,
        private readonly int $type,
        private readonly string $date,
        private readonly string $dateTime,
    )
    {
    }

    /**
     * @param int $companyId
     * @param int $employeeId
     * @param int $type
     * @param string $date
     * @param string $dateTime
     * @return $this
     */
    public static function create(
        int $companyId,
        int $employeeId,
        int $type,
        string $date,
        string $dateTime,
    ): self
    {
        $id = $employeeId . str_replace('-', '', $date) . $type;
        return new self(
            $id,
            $companyId,
            $employeeId,
            $type,
            $date,
            $dateTime
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
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
    public function getDateTime(): string
    {
        return $this->dateTime;
    }
}