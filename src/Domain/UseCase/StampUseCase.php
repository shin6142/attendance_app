<?php

namespace AttendanceApp\Src\Domain\UseCase;

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
     * @return array
     * @throws Exception
     */
    static public function getBy(int $company_id, int $employee_id, string $base_date): array
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();

        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
            $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE company_id = :company_id AND employee_id = :employee_id AND base_date = :base_date ORDER BY type");
        $stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
        $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $base_date, PDO::PARAM_STR);

        $res = $stmt->execute();
        if( $res ) {
            $data = $stmt->fetchAll();
        }
        return $data;
    }

    public function getByNew(int $company_id, int $employee_id, string $base_date): array
    {
        return $this->StampRepository->findBy($company_id, $employee_id, $base_date);
    }
}