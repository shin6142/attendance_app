<?php

require_once(__DIR__ . "/../../../../vendor/autoload.php");
// require_once("/vendor/autoload.php");
require_once(__DIR__ . "/Main.php");
require_once(__DIR__ . "/DB.php");

use PHPUnit\Framework\TestCase;


class MainTest extends TestCase
{

    public function test_makeHandle()
    {
        $db = new DB();
        $main = new Main($db);
        $actual = $main->makeHandle(1884310, 1164735, "2023-04-30");
        $expected = array(
            'employee_id' => 1164735,
            'company_id' => 1884310,
            'base_date' => "2023-04-30",
            'start_datetime' => "2023-04-30 12:04:06",
            'leave_datetime' => "2023-04-30 13:04:06",
            'back_datetime' => "2023-04-30 14:04:06",
            'end_datetime' => "2023-04-30 15:04:06"
        );

        $this->assertEquals($expected, $actual);
    }
}