<?php

namespace AttendanceApp\Src\Context\stamp\Domain\Service;

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use InvalidArgumentException;

class DailyStampsService
{

    public function getByType(int $employeeId, string $date, Stamps $stamps, int $type): null|Stamp
    {
        $this->validate($employeeId, $date, $stamps);
        foreach ($stamps->getStamps() as $stamp) {
            if ($type == $stamp->getType()) {
                return $stamp;
            }
        }
        return null;
    }

    public function lastStatus(int $employeeId, string $date, Stamps $stamps): int
    {
        $this->validate($employeeId, $date, $stamps);
        $lastStatus = 0;
        foreach ($stamps->getStamps() as $stamp) {
            $type = $stamp->getType();
            if ($lastStatus < $type) {
                $lastStatus = $type;
            }
        }
        return $lastStatus;
    }

    private function validate(int $employeeId, string $date, Stamps $stamps): void
    {
        $type = 0;
        foreach ($stamps->getStamps() as $stamp) {
            if ($date != $stamp->getDate()) {
                throw new InvalidArgumentException('日付と処理対象の打刻情報が一致しません');
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