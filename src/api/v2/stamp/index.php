<?php

require_once(__DIR__ . "/Main.php");


$result['success'] = false;
$result["error"] = '';
try {
    $company_id = $_REQUEST['company_id'];
    $employee_id = $_REQUEST['employee_id'];
    $base_date = $_REQUEST['base_date'];

    $resultArr = Main::makeHandle($company_id, $employee_id, $base_date);

    $result["content"] = $resultArr;
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;

//echo Main::response();
