<?php

namespace AttendanceApp\Src\Domain\Service;

use AttendanceApp\Src\Domain\Model\Stamps;
use InvalidArgumentException;

class DailyStampsService
{
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
        foreach ($stamps->getStamps() as $stamp) {
            if($date != $stamp->getDate()){
                throw new InvalidArgumentException('日付と処理対象の打刻情報が一致しません');
            }
            if($employeeId != $stamp->getEmployeeId()){
                throw new InvalidArgumentException('従業員IDと処理対象の打刻情報が一致しません');
            }
        }
    }
}