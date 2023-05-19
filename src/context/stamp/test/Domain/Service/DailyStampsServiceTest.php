<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\Service;

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use AttendanceApp\Src\Context\stamp\Domain\Service\DailyStampsService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DailyStampsServiceTest extends TestCase
{
    public function setUp(): void
    {
        $this->service = new DailyStampsService();
    }

    public function test_getByType()
    {
        //given
        $stamp_1 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 10:00:00');
        $stamps = new Stamps([$stamp_1]);
        $employeeId = 1;
        $date = '2023-04-01';
        //when
        $actual = $this->service->getByType($employeeId, $date, $stamps, 1);
        //then
        $this->assertEquals($stamp_1, $actual);
    }

    public function test_getByType_打刻履歴が存在しない場合()
    {
        //given
        $stamps = new Stamps([]);
        $employeeId = 1;
        $date = '2023-04-01';
        //when
        $actual = $this->service->getByType($employeeId, $date, $stamps, 1);
        //then
        $this->assertEquals(false, $actual);
    }
}