<?php

namespace AttendanceApp\Src\Inteface\Database;

use AttendanceApp\Src\Domain\Model\Stamp;
use AttendanceApp\Src\Domain\Model\Stamps;
use AttendanceApp\Src\Inteface\Gateway\StampGateway;
use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

class StampRepository implements StampGateway
{
    private PDO $pdo;
    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
        $dotenv->load();

        $dsn = 'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=mysql';
        $user = $_ENV['MYSQL_USER'];
        $password = $_ENV['MYSQL_PASSWORD'];
        try {
            $this->pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function findBy(int $companyId, int $employeeId, string $date): Stamps
    {
        $stmt = $this->pdo->prepare("SELECT * FROM attendance WHERE company_id = :company_id AND employee_id = :employee_id AND base_date = :base_date ORDER BY type");
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_STR);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $date, PDO::PARAM_STR);

        $res = $stmt->execute();
        $data = [];
        if( $res ) {
            $data = $stmt->fetchAll();
        }
        $list = [];
        foreach ($data as $d){
            $stamp = Stamp::create(
                $d['company_id'],
                $d['employee_id'],
                $d['type'],
                $d['base_date'],
                $d['datetime'],
            );
            $list[] = $stamp;
        }

        return new Stamps($list);
    }

    public function add(int $companyId, int $employeeId, int $type, string $date, string $datetime): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO attendance (
                employee_id, company_id, type, base_date, datetime
            ) VALUES (
                :employee_id, :company_id, :type, :base_date, :datetime
            )");
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_STR);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':base_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
        $res = $stmt->execute();
        if (!$res) {
            throw new Exception('打刻情報のDB登録に失敗しました');
        }
    }
}