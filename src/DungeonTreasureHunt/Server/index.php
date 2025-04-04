<?php

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require __DIR__ . '/Backend/JWT.php';

ini_set('html_errors', false);

$method = $_SERVER["REQUEST_METHOD"];


$users = [
    "admin" => "1234",
    "user" => "abcd"
];

//$routes = [
//  "POST" => [
//      "/login" => function () {
//
//      }
//  ]
//];
//
//$routes->add("GET", "/pepe", function($_SERVER){
//
//}, );

//class Test {
//    function __invoke(){
//
//     }
//}
//
//$myInvokableClass = new Test();
//
//$myInvokableClass();


if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/login") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    header("Content-Type: application/json");
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(["error" => "Faltan datos"]);
        exit;
    }

    $username = $data['username'];
    $password = $data['password'];

    if (!isset($users[$username]) || $users[$username] !== $password) {
        echo json_encode(["error" => "Credenciales incorrectas"]);
        exit;
    }

    $token = JwtHandler::generateToken(["username" => $username]);
    echo json_encode(["token" => $token]);
    exit;
}


if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/play") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    header("Content-Type: application/json");
    if (!$data) {
        error_log("Error al decodificar el JSON recibido: " . $input);
        echo json_encode(['error' => 'No se pudo procesar el grid']);
        exit;
    }

    $explorer = new \DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer();
    $path = $explorer->findPathToTreasure($data);
    echo json_encode($path);
    exit;
}


if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/grids") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    header("Content-Type: application/json");
    $headers = getallheaders();

    error_log("Input recibido: " . $input);
    if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Token no proporcionado"]);
        exit;
    }
    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $userData = JwtHandler::verifyToken($token);

    if (!$userData) {
        echo json_encode(["error" => "Token inválido o expirado"]);
        exit;
    }


    $data = json_decode($input, true);
    if (!isset($data['gridName']) || !isset($data['grid'])) {
        echo json_encode(["error" => "Grid o nombre del grid no proporcionado"]);
        exit;
    }

    $gridName = $data['gridName'];
    $grid = $data['grid'];

    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";

    $storedGrids = [];
    $newId = 1;

    if (file_exists($path)) {
        $storedGrids = json_decode(file_get_contents($path), true);

        $maxId = max([0, ...array_keys($storedGrids)]);
        $newId = $maxId + 1;
    }


    $storedGrids[$newId] = [
        'gridName' => $gridName,
        'grid' => $grid
    ];


    if (file_put_contents($path, json_encode($storedGrids))) {
        echo json_encode(["success" => true, "message" => "Grid guardado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al guardar el grid en el archivo"]);
    }
    exit;
}

if ($method === "GET" && $_SERVER['REQUEST_URI'] === "/grids") {
    header("Content-Type: application/json");
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Token no proporcionado"]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $userData = JwtHandler::verifyToken($token);

    if (!$userData) {
        echo json_encode(["error" => "Token inválido o expirado"]);
        exit;
    }


    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";

    if (!file_exists($path)) {
        echo json_encode(["success" => true, "grids" => []]);
        exit;
    }


    $fileContent = file_get_contents($path);

    if (empty($fileContent)) {
        echo json_encode(["success" => true, "grids" => []]);
        exit;
    }

    $grids = json_decode($fileContent, true);

    if ($grids === null) {
        echo json_encode(["error" => "Error al leer el contenido del archivo"]);
        exit;
    }

    echo json_encode(["success" => true, "grids" => $grids]);
    exit;
}