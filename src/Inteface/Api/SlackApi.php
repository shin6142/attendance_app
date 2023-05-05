<?php

namespace AttendanceApp\Src\Inteface\Api;

use AttendanceApp\Src\Inteface\Gateway\SlackAPIGateway;
use Dotenv\Dotenv;

class SlackApi implements SlackAPIGateway
{

    private string $token;
    public function __constructor(){
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();
        $this->token = $_ENV['SLACK_TOKEN'];
    }

    /**
     * @param string $channelId
     * @param string $text
     * @return void
     */
    public function sendMessage(string $channelId, string $text): void
    {
        //POSTデータ
        $params = http_build_query(
            [
                'channel' => $channelId,
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
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $result = json_decode($body, true);
        if (!$result["ok"]) {
            throw new Exception('スラック投稿に失敗しました');
        }
    }
}