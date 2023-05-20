<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\Model;

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use AttendanceApp\Src\Context\stamp\Domain\Model\Stamps;
use PHPUnit\Framework\TestCase;

class StampsTest extends TestCase
{
    public function setUp(): void
    {
        $this->stamp_1_20230401 = Stamp::create(
            1,
            1,
            1,
            '2023-04-01',
            '2023-04-01 08:00:00'
        );
        $this->stamp_2_20230401 = Stamp::create(
            1,
            2,
            1,
            '2023-04-01',
            '2023-04-01 08:00:00'
        );
        $this->stamp_1_20230402 = Stamp::create(
            1,
            1,
            2,
            '2023-04-02',
            '2023-04-02 08:00:00'
        );
    }

    public function test_filterByDate()
    {
        //given
        $stamps = new Stamps([$this->stamp_1_20230401, $this->stamp_1_20230402, $this->stamp_2_20230401,]);
        //when
        $actual = $stamps->filterByDate('2023-04-01');
        //then
        $expect = new Stamps([$this->stamp_1_20230401, $this->stamp_2_20230401]);
        $this->assertEquals($expect, $actual);
    }

    public function test_filterByEmployeeId()
    {
        //given
        $stamps = new Stamps([$this->stamp_1_20230401, $this->stamp_2_20230401, $this->stamp_1_20230402]);
        //when
        $actual = $stamps->filterByEmployeeId(1);
        //then
        $expect = new Stamps([$this->stamp_1_20230401, $this->stamp_1_20230402]);
        $this->assertEquals($expect, $actual);
    }
}