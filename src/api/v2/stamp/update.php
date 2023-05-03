<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use AttendanceApp\Src\Domain\UseCase\StampUseCase;

require_once(__DIR__ . "/../../v1/stamp/Main.php");

$result['success'] = false;
$result["error"] = '';
try{
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];

    if(!isset($company_id)){
        throw new InvalidArgumentException('会社IDを指定してください');
    }
    if(!isset($employee_id)){
        throw new InvalidArgumentException('従業員IDを指定してください');
    }
    StampUseCase::record((int)$company_id, (int)$employee_id);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
}catch (InvalidArgumentException $e){
    $result['error']['exception'] = $e->getMessage();
    header('HTTP', true, 400);
}catch(Exception $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;