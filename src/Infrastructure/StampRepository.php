<?php

namespace AttendanceApp\Src\Infrastructure\Gateway;

use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use Dotenv\Dotenv;

class StampRepository implements StampGateway
{

    public function findBy(int $companyId, int $employeeId, string $date): array
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
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
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_STR);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $date, PDO::PARAM_STR);

        $res = $stmt->execute();
        $data = [];
        if( $res ) {
            $data = $stmt->fetchAll();
        }
        return $data;
    }
}