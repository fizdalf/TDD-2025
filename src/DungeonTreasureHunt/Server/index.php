<?php

use DungeonTreasureHunt\Backend\Response;

require_once __DIR__ . '/routes.php';



ini_set('html_errors', false);


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

global $router;

$controller = $router->getController($uri, $method);

if (!$controller) {
    header("Content-Type: application/json");
    http_response_code(404);
    echo json_encode(["error" => "Ruta no encontrada"]);
    exit;
}

[$controllerFunction, $params] = $controller;
$result = $controllerFunction($params ?? []);
if($result instanceof Response){
    $result->send();
}
