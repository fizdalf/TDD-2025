<?php

namespace DungeonTreasureHunt;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require_once __DIR__ .'/Backend/Router.php';
require __DIR__ . '/Backend/JWT.php';
require_once __DIR__.'/Backend/Response.php';

require_once __DIR__.'/Backend/LoginController.php';
require_once __DIR__.'/Backend/PlayController.php';
require_once __DIR__.'/Backend/GridsPostController.php';
require_once __DIR__.'/Backend/GridsGetController.php';
require_once __DIR__.'/Backend/GridsDeleteController.php';

use DungeonTreasureHunt\Backend\Router;
use DungeonTreasureHunt\Backend\GridsDeleteController;
use DungeonTreasureHunt\Backend\GridsGetController;
use DungeonTreasureHunt\Backend\GridsPostController;
use DungeonTreasureHunt\Backend\LoginController;
use DungeonTreasureHunt\Backend\PlayController;


$router = new Router();

$router->register('/login', 'POST', new LoginController());
$router->register('/play', 'POST', new PlayController());
$router->register('/grids', 'POST', new GridsPostController());
$router->register('/grids', 'GET', new GridsGetController());
$router->register('/grids/{id}', 'DELETE', new GridsDeleteController());


//$router->register('/login', 'POST', function () {
//
//    $response = new Response();
//
//    $users = [
//        "admin" => "1234",
//        "user" => "abcd"
//    ];
//
//    $input = json_decode(file_get_contents("php://input"), true);
//
//    if (!isset($input['username'], $input['password'])) {
//        $response->setStatusCode(400);
//        $response->setJsonBody(["error" => "Faltan datos"]);
//        $response->send();
//        return;
//    }
//
//    $username = $input['username'];
//    $password = $input['password'];
//
//    if (!isset($users[$username]) || $users[$username] !== $password) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Credenciales incorrectas"]);
//        $response->send();
//        return;
//    }
//
//    $token = JwtHandler::generateToken(["username" => $username]);
//    $response->setJsonBody(["token" => $token]);
//    $response->send();
//});

//$router->register('/play', 'POST', function () {
//    $response = new Response();
//    $input = json_decode(file_get_contents("php://input"), true);
//
//    if (!$input) {
//        $response->setStatusCode(400);
//        $response->setJsonBody(["error" => "No se pudo procesar el grid"]);
//        $response->send();
//        return;
//    }
//
//    $explorer = new Backend\DungeonTreasureHuntExplorer();
//    $path = $explorer->findPathToTreasure($input);
//    $response->setJsonBody($path);
//    $response->send();
//
//});

//$router->register('/grids', 'POST', function () {
//    $response = new Response();
//    $headers = getallheaders();
//    $input = file_get_contents("php://input");
//
//    if (!isset($headers['Authorization'])) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token no proporcionado"]);
//        $response->send();
//        return;
//    }
//
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token inv\u00e1lido o expirado"]);
//        $response->send();
//        return;
//    }
//
//    $data = json_decode($input, true);
//    if (!isset($data['grid'], $data['gridName'])) {
//        $response->setStatusCode(400);
//        $response->setJsonBody(["error" => "Faltan datos"]);
//        $response->send();
//        return;
//    }
//
//    $path = __DIR__ . "/{$userData['username']}_gridSaved.txt";
//    $storedGrids = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
//    $newId = empty($storedGrids) ? 1 : max(array_keys($storedGrids)) + 1;
//
//    $storedGrids[$newId] = ["gridName" => $data['gridName'], "grid" => $data['grid']];
//
//    if (file_put_contents($path, json_encode($storedGrids))) {
//        $response->setJsonBody(["success" => true]);
//    } else {
//        $response->setStatusCode(500);
//        $response->setJsonBody(["error" => "No se pudo guardar"]);
//    }
//    $response->send();
//});

//$router->register('/grids', 'GET', function () {
//    $response = new Response();
//    $headers = getallheaders();
//
//    if (!isset($headers['Authorization'])) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token no proporcionado"]);
//        $response->send();
//        return;
//    }
//
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token inv\u00e1lido o expirado"]);
//        $response->send();
//        return;
//    }
//
//    $path = __DIR__ . "/{$userData['username']}_gridSaved.txt";
//
//    if (!file_exists($path)) {
//        $response->setJsonBody(["success" => true, "grids" => []]);
//        $response->send();
//        return;
//    }
//
//    $fileContent = file_get_contents($path);
//    $grids = json_decode($fileContent, true) ?: [];
//
//    $response->setJsonBody(["success" => true, "grids" => $grids]);
//    $response->send();
//
//});
//$router->register('/grids/{id}', 'DELETE', function ($params) {
//    $response = new Response();
//    $headers = getallheaders();
//
//    if (!isset($headers['Authorization'])) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token no proporcionado"]);
//        $response->send();
//        return;
//    }
//
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        $response->setStatusCode(401);
//        $response->setJsonBody(["error" => "Token inv\u00e1lido o expirado"]);
//        $response->send();
//        return;
//    }
//
//    $idToDelete = $params['id'] ?? null;
//    if ($idToDelete === null) {
//        $response->setStatusCode(400);
//        $response->setJsonBody(["error" => "ID no proporcionado"]);
//        $response->send();
//        return;
//    }
//
//    $path = __DIR__ . "/{$userData['username']}_gridSaved.txt";
//
//    if (!file_exists($path)) {
//        $response->setStatusCode(404);
//        $response->setJsonBody(["error" => "No se encontr\u00f3 el archivo"]);
//        $response->send();
//        return;
//    }
//
//    $grids = json_decode(file_get_contents($path), true);
//    if (!isset($grids[$idToDelete])) {
//        $response->setStatusCode(404);
//        $response->setJsonBody(["error" => "Grid no encontrado"]);
//        $response->send();
//        return;
//    }
//
//    unset($grids[$idToDelete]);
//    file_put_contents($path, json_encode($grids));
//
//    $response->setJsonBody(["success" => true]);
//    $response->send();
//});
