<?php


use DungeonTreasureHunt\Framework\http\APIResponse;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\http\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/routes.php';

ini_set('html_errors', false);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

global $router;

$controller = $router->getController($uri, $method);

if (!$controller) {
    //TODO: see how we can replace this with an abstraction we know!
    APIResponse::error("Ruta no encontrada", 404)->send();
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
    $result = APIResponse::error('Internal Server Error', 500);
}
if ($result instanceof Response) {
    $result->send();
}