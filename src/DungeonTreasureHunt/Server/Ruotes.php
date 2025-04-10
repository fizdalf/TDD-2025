<?php

namespace DungeonTreasureHunt;

use JwtHandler;

function getRoutes(): array
{
    return [
        "POST" => [
            "/login" => function () {

                $users = [
                    "admin" => "1234",
                    "user" => "abcd"
                ];
                header("Content-Type: application/json");
                $input = json_decode(file_get_contents("php://input"), true);
                if (!isset($input['username']) || !isset($input['password'])) {
                    echo json_encode(["error" => "Faltan datos"]);
                    return;
                }

                $username = $input['username'];
                $password = $input['password'];

                if (!isset($users[$username]) || $users[$username] !== $password) {
                    echo json_encode(["error" => "Credenciales incorrectas"]);
                    return;
                }

                $token = JwtHandler::generateToken(["username" => $username]);
                echo json_encode(["token" => $token]);
            },

            "/play" => function () {
                header("Content-Type: application/json");
                $input = json_decode(file_get_contents("php://input"), true);
                if (!$input) {
                    echo json_encode(["error" => "No se pudo procesar el grid"]);
                    return;
                }

                $explorer = new \DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer();
                $path = $explorer->findPathToTreasure($input);
                echo json_encode($path);
            },

            "/grids" => function () {
                header("Content-Type: application/json");
                $headers = getallheaders();
                $input = file_get_contents("php://input");
                error_log("POST /grids input: $input");

                if (!isset($headers['Authorization'])) {
                    echo json_encode(["error" => "Token no proporcionado"]);
                    return;
                }

                $token = str_replace("Bearer ", "", $headers['Authorization']);
                $userData = JwtHandler::verifyToken($token);

                if (!$userData) {
                    echo json_encode(["error" => "Token inválido o expirado"]);
                    return;
                }

                $data = json_decode($input, true);
                if (!isset($data['grid']) || !isset($data['gridName'])) {
                    echo json_encode(["error" => "Faltan datos"]);
                    return;
                }

                $path = __DIR__ . "{$userData['username']}_gridSaved.txt";

                $storedGrids = [];
                $newId = 1;

                if (file_exists($path)) {
                    $storedGrids = json_decode(file_get_contents($path), true);
                    $maxId = max([0, ...array_keys($storedGrids)]);
                    $newId = $maxId + 1;
                }

                $storedGrids[$newId] = [
                    "gridName" => $data['gridName'],
                    "grid" => $data['grid']
                ];

                if (file_put_contents($path, json_encode($storedGrids))) {
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["error" => "No se pudo guardar"]);
                }
            }
        ],
        "GET" => [
            "/grids" => function () {
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

        ],
        "DELETE" => [
            "/grids/{id}" => function ($params) {
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

                $idToDelete = $params['id'] ?? null;

                if ($idToDelete === null) {
                    echo json_encode(["error" => "ID no proporcionado"]);
                    exit;
                }

                $path = __DIR__ . "{$userData['username']}_gridSaved.txt";

                if (!file_exists($path)) {
                    echo json_encode(["error" => "No se encontró el archivo"]);
                    exit;
                }

                $grids = json_decode(file_get_contents($path), true);

                if (!isset($grids[$idToDelete])) {
                    echo json_encode(["error" => "Grid no encontrado"]);
                    exit;
                }

                unset($grids[$idToDelete]);

                file_put_contents($path, json_encode($grids));
                echo json_encode(["success" => true]);
                exit;
            }
        ]
    ];
}