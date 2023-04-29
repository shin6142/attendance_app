<?php

require_once(__DIR__ . "/Main.php");

$result['success'] = false;
$result["error"] = '';
try{
    TokenRefresher::run();
    $result['success'] = true;
    header("HTTP/1.1 200 OK");
}catch(Exception $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
exit;