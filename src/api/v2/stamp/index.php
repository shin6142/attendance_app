<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use AttendanceApp\Src\Context\stamp\Infrastructure\Injector\Injector;
use AttendanceApp\Src\Context\stamp\Inteface\Controller\GetRequest;


$result['success'] = false;
$result["error"] = '';
try {
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];
    $base_date = $_REQUEST['base_date'];

    if (!isset($company_id)) {
        throw new InvalidArgumentException('会社IDを指定してください');
    }
    if (!isset($employee_id)) {
        throw new InvalidArgumentException('従業員IDを指定してください');
    }
    if (!isset($base_date)) {
        throw new InvalidArgumentException('打刻日を指定してください');
    }
    $request = new GetRequest($company_id, $employee_id, $base_date);
    $controller = Injector::getStampController();
    $result["content"] = $controller->getStampsByDate($request);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
} catch (InvalidArgumentException $e){
    $result['error']['exception'] = $e->getMessage();
    header('HTTP', true, 400);
}catch (Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    $result['error']['exception'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;