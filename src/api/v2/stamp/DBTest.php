<?php

require_once(__DIR__ . "/../../../../vendor/autoload.php");
require_once(__DIR__ . "/DB.php");

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{

  public function test_connectionDB()
  {
    $actual = DB::connectionDB(1884310, 1164735, "2023-04-30");
    print_r($actual);
    $expected = [
      [
        "id" => 1,
        "employee_id" => 1164735,
        "company_id" => 1884310,
        "type" => "1",
        "base_date" => "2023-04-30",
        "datetime" => "2023-04-30 12:04:06",
        "created_at" => "2023-06-16 00:43:01"
      ],
      [
        "id" => 2,
        "employee_id" => 1164735,
        "company_id" => 1884310,
        "type" => "2",
        "base_date" => "2023-04-30",
        "datetime" => "2023-04-30 13:04:06",
        "created_at" => "2023-06-16 00:43:01"
      ],
      [
        "id" => 3,
        "employee_id" => 1164735,
        "company_id" => 1884310,
        "type" => "3",
        "base_date" => "2023-04-30",
        "datetime" => "2023-04-30 14:04:06",
        "created_at" => "2023-06-16 00:43:01"
      ],
      [
        "id" => 4,
        "employee_id" => 1164735,
        "company_id" => 1884310,
        "type" => "4",
        "base_date" => "2023-04-30",
        "datetime" => "2023-04-30 15:04:06",
        "created_at" => "2023-06-16 00:43:01"
      ]
    ];

    $this->assertEquals($expected, $actual);
  }
}