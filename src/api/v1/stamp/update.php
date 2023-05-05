<?php

require_once(__DIR__ . "/Main.php");

$result['success'] = false;
$result["error"] = '';
try {

    if (!isset($_POST['company_id'])) {
        throw new Exception('不正な会社IDです');
    }
    if (!isset($_POST['employee_id'])) {
        throw new Exception('不正な従業員IDです');
    }
    Main::record($_POST['company_id'], $_POST['employee_id']);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

echo json_encode($result);
exit;