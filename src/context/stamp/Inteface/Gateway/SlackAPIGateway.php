<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Gateway;

Interface SlackAPIGateway
{

    public function send(int $status): void;
}