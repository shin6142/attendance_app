<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;

interface FreeeApiGateway
{
    public function getEmployeeInfo(): void;

    public function registerAttendance(): void;
}