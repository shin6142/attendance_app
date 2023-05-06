<?php

namespace AttendanceApp\Src\Inteface\Api;

use AttendanceApp\Src\Inteface\Gateway\SlackAPIGateway;
use Exception;

class SlackApi implements SlackAPIGateway
{

    private string $token;
    private string $channelId;

    public function __construct()
    {
        $this->token = $_ENV['SLACK_TOKEN'];
        $this->channelId = $_ENV['CHANNEL_TIMES_YAMAGA'];
    }

    /**
     * @param string $text
     * @return void
     * @throws Exception
     */
    public function sendMessage(string $text): void
    {
        //POSTデータ
        $params = http_build_query(
            [
                'channel' => $this->channelId,
                'text' => $text
            ]
        );

        // SLACK POSTリクエスト送信
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://slack.com/api/chat.postMessage');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $this->token"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        $result = json_decode($body, true);
        if (!$result["ok"]) {
            throw new Exception('スラック投稿に失敗しました');
        }
    }
}