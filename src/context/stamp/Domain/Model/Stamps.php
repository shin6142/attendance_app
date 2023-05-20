<?php

namespace AttendanceApp\Src\Context\stamp\Domain\Model;

class Stamps
{
    /**
     * @param Stamp[] $stamps
     */
    public function __construct(private readonly array $stamps)
    {
    }

    /**
     * @return array
     */
    public function getStamps(): array
    {
        return $this->stamps;
    }

    public function filterByEmployeeId(int $id): self
    {
        $filtered = array_filter($this->stamps, function ($stamp) use ($id) {
            return $stamp->getEmployeeId() == $id;
        });
        return new self(array_values($filtered));
    }

    public function filterByDate(string $date): self
    {
        $filtered = array_filter($this->stamps, function ($stamp) use ($date) {
            return $stamp->getDate() == $date;
        });
        return new self(array_values($filtered));
    }

}