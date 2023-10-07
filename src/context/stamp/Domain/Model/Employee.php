<?php

namespace AttendanceApp\Src\Context\stamp\Domain\Model;

class Employee
{
    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $companyId
     * @param string $date
     * @param string $clockInDateTime
     * @param ClockInHistory $clockInHistory
     * @return Stamp
     */
    public function clockIn(int $companyId, string $date, string $clockInDateTime, ClockInHistory $clockInHistory): Stamp
    {
        $type = $clockInHistory->nextType();
        return Stamp::create(
            $this->getId(),
            $companyId,
            $type,
            $date,
            $clockInDateTime
        );
    }


}