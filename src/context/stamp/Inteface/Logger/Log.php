<?php

namespace AttendanceApp\Src\Context\stamp\Inteface\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Log
{
    const LOG_FILE_PATH = __DIR__ . '/../../../logs/record.log';
    public static function logInfo(string $json, string $channelName): void
    {
        // タイムゾーン設定
        date_default_timezone_set("Asia/Tokyo");
        // フォーマッタの作成
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel% %level_name% %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        // ハンドラの作成
        $stream = new StreamHandler(self::LOG_FILE_PATH, Level::Info); // ログレベルINFO以上のみ出力
        $stream->setFormatter($formatter);

        // ロガーオブジェクトの作成
        $logger = new Logger($channelName);
        $logger->pushHandler($stream);
        $logger->info($json); // 出力される
    }
}