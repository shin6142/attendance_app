<?php

namespace AttendanceApp\Src\Inteface\Gateway;

Interface SlackAPIGateway
{
    /**
     * @param string $text
     * @return void
     */
    public function sendMessage(string $text): void;
}