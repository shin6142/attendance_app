<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use AttendanceApp\Src\Infrastructure\Injector\Injector;


$result['success'] = false;
$result["error"] = '';
try{
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];
    $base_date = $_REQUEST['base_date'];

    if(!isset($company_id)){
        throw new Exception('会社IDを指定してください');
    }
    if(!isset($employee_id)){
        throw new Exception('従業員IDを指定してください');
    }
    if(!isset($base_date)){
        throw new Exception('打刻日を指定してください');
    }

    $controller = Injector::getStampController();
    $result["content"] = $controller->getStampsByDate($company_id, $employee_id, $base_date);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
}catch(Throwable $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;