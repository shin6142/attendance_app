<?php

namespace AttendanceApp\Src\Domain\UseCase;

use AttendanceApp\Src\Domain\Model\Stamp;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class StampUseCase
{

    public function __construct(private readonly StampGateway $StampRepository)
    {
    }

    /**
     * @param int $company_id
     * @param int $employee_id
     * @param string $base_date
     * @return Stamp
     */
    public function getBy(int $company_id, int $employee_id, string $base_date): Stamp
    {
        return $this->StampRepository->findBy($company_id, $employee_id, $base_date);
    }
}