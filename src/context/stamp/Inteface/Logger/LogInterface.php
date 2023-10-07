<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Logger;


interface LogInterface
{
    public static function logInfo(string $json, string $channelName): void;
}