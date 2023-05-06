<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;

Interface SlackAPIGateway
{
    /**
     * @param string $text
     * @return void
     */
    public function sendMessage(string $text): void;
}