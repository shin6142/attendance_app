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


    public function test_lastStatus()
    {
        //given
        $stamp_1 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 10:00:00');
        $stamps = new Stamps([$stamp_1]);
        $employeeId = 1;
        $date = '2023-04-01';
        //when
        $actual = $this->service->lastStatus($employeeId, $date, $stamps);
        //then
        $expect = 1;
        $this->assertEquals($expect, $actual);
    }

    public function test_lastStatus_従業員IDが不正値()
    {
        //then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('従業員IDと処理対象の打刻情報が一致しません');
        //given
        $stamp_1 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 10:00:00');
        $stamps = new Stamps([$stamp_1]);
        $InvalidEmployeeId = 2;
        $date = '2023-04-01';
        //when
        $this->service->lastStatus($InvalidEmployeeId, $date, $stamps);
    }

    public function test_lastStatus_日付が不正値()
    {
        //then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('日付と処理対象の打刻情報が一致しません');
        //given
        $stamp_1 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 10:00:00');
        $stamps = new Stamps([$stamp_1]);
        $InvalidEmployeeId = 2;
        $date = '2023-04-02';
        //when
        $this->service->lastStatus($InvalidEmployeeId, $date, $stamps);
    }

    public function test_lastStatus_打刻タイプが不正値()
    {
        //then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('重複した打刻タイプの打刻情報が存在します');
        //given
        $stamp_1 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 10:00:00');
        $stamp_2 = Stamp::create(1, 1, 1, '2023-04-01', '2023-04-01 12:00:00');
        $stamps = new Stamps([$stamp_1, $stamp_2]);
        $employeeId = 1;
        $date = '2023-04-01';
        //when
        $this->service->lastStatus($employeeId, $date, $stamps);
    }
}