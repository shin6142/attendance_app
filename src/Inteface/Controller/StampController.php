<?php

namespace AttendanceApp\Src\Inteface\Controller;

use AttendanceApp\Src\Domain\UseCase\StampUseCase;

class StampController
{
    public function __construct(private readonly StampUseCase $useCase){}

    public function getBy(): StampsDto
    {
        return new StampsDto();
    }
}