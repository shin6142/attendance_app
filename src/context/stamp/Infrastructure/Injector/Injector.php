<?php

namespace AttendanceApp\Src\Context\stamp\Infrastructure\Injector;

require_once __DIR__ . "/../../../../../vendor/autoload.php";


use AttendanceApp\Src\Context\stamp\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Context\stamp\Inteface\Controller\StampController;
use AttendanceApp\Src\Context\stamp\Inteface\Database\StampRepository;

class Injector
{
    public static function getStampController(): StampController
    {
        $repository = new StampRepository();
        $useCase = new StampUseCase($repository);
        return new StampController($useCase);
    }
}