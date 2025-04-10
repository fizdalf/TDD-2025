<?php

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Router;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require __DIR__ . '/Backend/JWT.php';
require_once __DIR__. '/Router.php';
require_once __DIR__. '/Routes.php';

ini_set('html_errors', false);

$router = new Router();

$routes = DungeonTreasureHunt\getRoutes();

foreach ($routes as $method => $methodRoutes) {
    foreach ($methodRoutes as $uri => $controller) {
        $router->register($uri, $method, $controller);
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = $router->getController($uri, $method);

if (!$controller) {
    http_response_code(404);
    echo json_encode(["error" => "Ruta no encontrada"]);
    exit;
}

[$controllerFunction, $params] = $controller;
$controllerFunction($params ?? []);