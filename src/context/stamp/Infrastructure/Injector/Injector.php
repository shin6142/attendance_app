<?php

namespace AttendanceApp\Src\Context\stamp\Infrastructure\Injector;

require_once __DIR__ . "/../../../../../vendor/autoload.php";


use AttendanceApp\Src\Context\stamp\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Context\stamp\Infrastructure\Api\FreeeApi;
use AttendanceApp\Src\Context\stamp\Infrastructure\Api\SlackApi;
use AttendanceApp\Src\Context\stamp\Infrastructure\Database\StampRepository;
use AttendanceApp\Src\Context\stamp\Inteface\Controller\StampController;

class Injector
{
    public static function getStampController(): StampController
    {
        $repository = new StampRepository();
        $slackApi = new SlackApi();
        $freeeApi = new FreeeApi();
        $useCase = new StampUseCase($repository, $slackApi, $freeeApi);
        return new StampController($useCase);
    }
}