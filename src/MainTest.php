<?php

require_once(__DIR__ . "/../vendor/autoload.php");
// require_once("/vendor/autoload.php");
require_once(__DIR__ . "/Main.php");

use PHPUnit\Framework\TestCase;


class MainTest extends TestCase{

    public function testExample() {
        $expected = 'hoge';
        $this->assertEquals($expected, 'hoge');
    }

    public function test_mainFunction() {
        $expected = 1;
        $actual = Main::mainFunc();
        $this->assertEquals($expected, $actual);
    }
}