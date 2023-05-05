<?php

namespace AttendanceApp\Src\Inteface\Gateway;

Interface SlackAPIGateway
{
    /**
     * @param string $channelId
     * @param string $text
     * @return void
     */
    public function sendMessage(string $channelId, string $text): void;
}