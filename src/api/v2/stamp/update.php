<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use AttendanceApp\Src\Context\stamp\Infrastructure\Injector\Injector;
use AttendanceApp\Src\Context\stamp\Inteface\Controller\PostRequest;


$result['success'] = false;
$result["error"] = '';
try {
    if (!isset($_POST['company_id'])) {
        throw new Exception('会社IDを指定してください');
    }
    if (!isset($_POST['employee_id'])) {
        throw new Exception('従業員IDを指定してください');
    }
    $controller = Injector::getStampController();
    $request = new PostRequest(
        $_POST['company_id'],
        $_POST['employee_id'],
        date("Y-m-d"),
        date("Y-m-d H:i:s")
    );
    $controller->record($request);
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
} catch (InvalidArgumentException $e) {
    $result['error']['exception'] = $e->getMessage();
    header('HTTP', true, 400);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;