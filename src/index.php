<?php

require_once(__DIR__ . "/Main.php");

$result['success'] = false;
$result["error"] = '';
try{
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];

    if(!isset($company_id)){
        throw new Exception('会社IDを指定してください');
    }
    if(!isset($employee_id)){
        throw new Exception('従業員IDを指定してください');
    }
    Main::record((int)$company_id, (int)$employee_id);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
}catch(Exception $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;