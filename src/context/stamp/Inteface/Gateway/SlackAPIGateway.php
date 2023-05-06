<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;

Interface SlackAPIGateway
{
    /**
     * @param int $status
     * @return void
     */
    public function sendMessage(int $status): void;
}