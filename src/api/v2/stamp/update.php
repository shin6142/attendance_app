<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use AttendanceApp\Src\Domain\UseCase\StampUseCase;


$result['success'] = false;
$result["error"] = '';
try{
    if (!isset($_POST['company_id'])) {
        throw new Exception('不正な会社IDです');
    }
    if (!isset($_POST['employee_id'])) {
        throw new Exception('不正な従業員IDです');
    }

    StampUseCase::record($_POST['company_id'], $_POST['employee_id']);
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