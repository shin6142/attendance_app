<?php

namespace AttendanceApp\Src\Infrastructure\Injector;

require_once __DIR__ . "/../../../vendor/autoload.php";


use AttendanceApp\Src\Domain\UseCase\StampUseCase;
use AttendanceApp\Src\Inteface\Controller\StampController;
use AttendanceApp\Src\Inteface\Database\StampRepository;

class Injector
{
    public static function getStampController(): StampController
    {
        $repository = new StampRepository();
        $useCase = new StampUseCase($repository);
        return new StampController($useCase);
    }
}