<?php

namespace AttendanceApp\Src\Context\stamp\test\Domain\Model;

require_once(__DIR__ . "/../../../../vendor/autoload.php");

use AttendanceApp\Src\Context\stamp\Domain\Model\Stamp;
use PHPUnit\Framework\TestCase;

class StampTest extends TestCase
{
    public function test_create()
    {
        // given
        $company_id = 1884310;
        $employee_id = 1164735;
        $type = 1;
        $base_date = '2023-04-29';
        $date_time = "2023-04-29 21:04:20";
        // when
        $stamp = Stamp::create(
            $company_id,
            $employee_id,
            $type,
            $base_date,
            $date_time
        );

        //then
        $this->assertEquals('1164735202304291', $stamp->getId());
    }
}