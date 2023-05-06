<?php

namespace AttendanceApp\Src\Domain\Service;

use AttendanceApp\Src\Domain\Model\Stamps;

class DailyStampsService
{
    public function lastStatus(int $employeeId, Stamps $stamps): int
    {
        $this->validate($employeeId, $stamps);
        $lastStatus = 0;
        foreach ($stamps->getStamps() as $stamp) {
            $type = $stamp->getType();
            if ($lastStatus < $type) {
                $lastStatus = $type;
            }
        }
        return $lastStatus;
    }

    private function validate(int $employeeId, Stamps $stamps): bool|\Exception
    {
        foreach ($stamps->getStamps() as $stamp) {
            if($employeeId != $stamp->getEmployeeId()){
                throw new \InvalidArgumentException('従業員IDと処理対象の打刻情報が一致しません');
            }
        }
        return true;
    }
}