<?php

namespace AttendanceApp\Src\Context\stamp\Domain\Model;

use InvalidArgumentException;

class ClockInHistory
{
    private function __construct(
        private readonly int    $employeeId,
        private readonly int    $companyId,
        private readonly Stamps $stamps
    )
    {
    }

    public static function create(int $employeeId, int $companyId, Stamps $stamps): self
    {
        self::validate($employeeId, $companyId, $stamps);
        return new self($employeeId, $companyId, $stamps);
    }

    public function getStamps(): Stamps
    {
        return $this->stamps;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getByType(int $type): null|Stamp
    {
        foreach ($this->stamps->getStamps() as $stamp) {
            if ($type == $stamp->getType()) {
                return $stamp;
            }
        }
        return null;
    }


    public function lastType(): int
    {
        $lastStatus = 0;
        foreach ($this->stamps->getStamps() as $stamp) {
            $type = $stamp->getType();
            if ($lastStatus < $type) {
                $lastStatus = $type;
            }
        }
        return $lastStatus;
    }

    public function nextType(): int
    {
        return 1 + $this->lastType();
    }

    private static function validate(int $employeeId, int $companyId, Stamps $stamps): void
    {
        $type = 0;
        foreach ($stamps->getStamps() as $stamp) {
            if ($companyId != $stamp->getCompanyId()) {
                throw new InvalidArgumentException('会社IDと処理対象の打刻情報が一致しません');
            }

            if ($employeeId != $stamp->getEmployeeId()) {
                throw new InvalidArgumentException('従業員IDと処理対象の打刻情報が一致しません');
            }

            if ($type != $stamp->getType()) {
                $type = $stamp->getType();
            } else {
                throw new InvalidArgumentException('重複した打刻タイプの打刻情報が存在します');
            }
        }
    }
}