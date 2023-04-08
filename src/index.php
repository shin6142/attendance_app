<?php

require_once(__DIR__ . "/Main.php");

$result['success'] = false;
$result["error"] = '';
try{
    $mode = 'dev';
    Main::record($mode);
    $result['success'] = true;
}catch(Exception $e){
    header('HTTP/1.1 500 Internal Server Error');
    $result["error"] = $e->getMessage();
}

header("HTTP/1.1 200 OK");
header('Content-Type: application/json');
echo json_encode($result);
exit;