<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Api;

use AttendanceApp\Src\Context\stamp\Inteface\Gateway\SlackAPIGateway;
use Dotenv\Dotenv;
use Exception;

class SlackApi implements SlackAPIGateway
{

    private string $token;
    private string $channelId;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../');
        $dotenv->load();
        $this->token = $_ENV['SLACK_TOKEN'];
        $this->channelId = $_ENV['CHANNEL_DEV_ATTENDANCE'];
    }

    /**
     * @param int $status
     * @return void
     * @throws Exception
     */
    public function sendMessage(int $status): void
    {
        //POSTデータ
        $params = http_build_query(
            [
                'channel' => $this->channelId,
                'text' => $this->message($status)
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

    private function message(int $status): string
    {
        $statusArr = [
            0 => "テスト打刻です",
            1 => "開始します。",
            2 => "離席します。",
            3 => "戻ります。",
            4 => "終了します。"
        ];
        return $statusArr[$status];
    }
}