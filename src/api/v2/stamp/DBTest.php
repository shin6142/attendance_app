<?php

require_once(__DIR__ . "/../../../../vendor/autoload.php");
require_once(__DIR__ . "/DB.php");

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{

  public function test_connectionDB()
  {
    $records = DB::select(1884310, 1164735, "2023-04-30");

    $actual = count($records);

    $this->assertEquals(4, $actual);
  }
}