<?php

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require __DIR__ . '/Backend/JWT.php';

header("Content-Type: application/json");

$database = new PDO(
    "mysql:host=localhost;port=3307;dbname=samu",
    "root",
    "=%@T,|Jr=/>b[Ze7ry=uHoHRms[k(ldb"
);
$queryResult = $database->query('SELECT CURRENT_TIMESTAMP;');
$queryResult->execute();

session_start();
$_SESSION["name"] = "Samu";
ini_set('html_errors', false);


$input = file_get_contents('php://input');
$data = json_decode($input, true);

$method = $_SERVER["REQUEST_METHOD"];
$action = $_GET["action"] ?? null;


$users = [
    "admin" => "1234",
    "user" => "abcd"
];


if ($method === "POST" && $action === "login") {
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


if ($method === "GET" && $action === "verify") {
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

    echo json_encode(["message" => "Token válido", "user" => $userData]);
    exit;
}


if ($method === "POST" && $action === "play") {
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


if ($method === "POST" && $action === "save-grid") {
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

    $data = json_decode($input, true);
    if (!isset($data['grid'])) {
        echo json_encode(["error" => "Grid no proporcionado"]);
        exit;
    }

    $grid = $data['grid'];

    $path = __DIR__ . "/Server/{$userData['username']}_gridSaved.txt";

    $gridContent = json_encode($grid);

    if (file_put_contents($path, $gridContent)) {
        echo json_encode(["success" => true, "message" => "Grid guardado correctamente en el archivo"]);
    } else {
        echo json_encode(["error" => "Error al guardar el grid en el archivo"]);
    }
    exit;
}