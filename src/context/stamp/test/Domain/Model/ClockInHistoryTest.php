<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\Model;

require_once(__DIR__ . "/../../../../../../vendor/autoload.php");

use AttendanceApp\Src\Context\stamp\Domain\Model\ClockInHistory;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use PHPUnit\Framework\TestCase;

class ClockInHistoryTest extends TestCase
{
    public function setUp(): void
    {
        $this->date = '2023-04-01';
        $datetime = '2023-04-01 12:00:00';
        $this->stamp = Stamp::create(
            1,
            2,
            1,
            $this->date,
            $datetime
        );
        $this->stamps = new Stamps([$this->stamp]);
        $this->employeeId = 1;
        $this->companyId = 2;
        $this->clockInHistory = ClockInHistory::create($this->employeeId, $this->companyId, $this->stamps);
    }

    public function test_create()
    {
        //when
        $actual = ClockInHistory::create($this->employeeId, $this->companyId, $this->stamps);
        //then
        $this->assertEquals(1, $actual->getEmployeeId());
        $this->assertEquals($this->stamps, $actual->getStamps());
    }

    public function test_lastType()
    {
        //when
        $actual = $this->clockInHistory->lastType();
        //then
        $this->assertEquals(1, $actual);
    }

    public function test_nextType()
    {
        // when
        $actual = $this->clockInHistory->nextType();
        // then
        $this->assertEquals(2, $actual);
    }

    public function test_getByType()
    {
        //when
        $actual = $this->clockInHistory->getByType(1);
        //then
        $expect = $this->stamp;
        $this->assertEquals($expect, $actual);
    }
}