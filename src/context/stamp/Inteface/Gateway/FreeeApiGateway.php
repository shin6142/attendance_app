<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;


use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use AttendanceApp\Src\Context\stamp\Domain\UseCase\DailyStampsDto;

interface FreeeApiGateway
{
    public function getEmployeeInfo(): void;

    public function registerAttendance(DailyStampsDto $dto): void;
}