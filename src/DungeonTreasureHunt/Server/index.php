<?php

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require __DIR__ . '/Backend/JWT.php';

ini_set('html_errors', false);


$input = file_get_contents('php://input');
$data = json_decode($input, true);

$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? null;


$users = [
    "admin" => "1234",
    "user" => "abcd"
];


if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/login") {
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


if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/save-grid") {
    header("Content-Type: application/json");
    $headers = getallheaders();

    error_log("Input recibido: ".$input);
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
    if (!isset($data['grid'])) {
        echo json_encode(["error" => "Grid no proporcionado"]);
        error_log("Datos recibidos: ".$data );
        exit;
    }

    $grid = $data['grid'];

    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";

    $Grids = [];
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $Grids = json_decode($content, true) ?: [];
    }

    $nextId = count($Grids) > 0 ? (max(array_keys($Grids)) + 1) : 1;

    $Grids[$nextId] = $grid;

    if (file_put_contents($path, json_encode($Grids, JSON_PRETTY_PRINT))) {
        echo json_encode([
            "success" => true,
            "message" => "Grid guardado correctamente",
            "id" => $nextId
        ]);
    } else {
        echo json_encode(["error" => "Error al guardar el grid"]);
    }
    exit;
}

