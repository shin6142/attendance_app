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

    public function filterByEmployeeId(): self
    {
        //TODO: 実装
        return new self([]);
    }

    public function filterByDate(): self
    {
        //TODO: 実装
        return new self([]);
    }

}