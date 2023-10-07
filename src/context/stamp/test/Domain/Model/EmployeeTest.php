<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\Model;

require_once(__DIR__ . "/../../../../../../vendor/autoload.php");

use AttendanceApp\Src\Context\stamp\Domain\Model\ClockInHistory;
use AttendanceApp\Src\Context\stamp\Domain\Model\Employee;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use PHPUnit\Framework\TestCase;

class EmployeeTest extends TestCase
{
    public function setUp(): void
    {
        $this->date = '2023-04-01';
        $this->datetime = '2023-04-01 12:00:00';
        $stamp = Stamp::create(
            1,
            1,
            1,
            $this->date,
            $this->datetime
        );
        $this->stamps = new Stamps([$stamp]);
        $this->employeeId = 1;
        $this->clockInHIstory = ClockInHistory::create($this->employeeId, $this->stamps);
        $this->employee = new Employee($this->employeeId);
        $this->companyId = 1;
    }

    public function test_clockIn()
    {
        //given
        //when
        $actual = $this->employee->clockIn($this->companyId, $this->date, $this->datetime, $this->clockInHIstory);
        //then
        $expect = Stamp::create(
            $this->companyId,
            $this->employeeId,
            2,
            $this->date,
            $this->datetime
        );
        $this->assertEquals($expect, $actual);
    }
}