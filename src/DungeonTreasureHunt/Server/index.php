<?php


use DungeonTreasureHunt\Backend\http\JsonResponseBuilder;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\Response;


require_once __DIR__ . '/../../../vendor/autoload.php';
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

[$controllerFunction, $routeParams] = $controller;

$request = new Request(
    headers: getallheaders(),
    params: $routeParams ?? [],
    body: file_get_contents("php://input")
);

try {

    $result = $controllerFunction($request);
} catch (Exception $exception) {
    $result = JsonResponseBuilder::internalServerError();
}
if ($result instanceof Response) {
    $result->send();
}

