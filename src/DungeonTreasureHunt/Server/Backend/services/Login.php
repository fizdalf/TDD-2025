<?php
require_once __DIR__ . '/JwtHandler.php'; // Incluir la clase JwtHandler

//// Simulamos una base de datos de usuarios con sus credenciales
//$users = [
//    "admin" => "1234",
//    "user" => "abcd"
//];
//
//header('Content-Type: application/json');
//
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    // Obtener el cuerpo de la solicitud y decodificarlo
//    $input = json_decode(file_get_contents('php://input'), true);
//
//    if (!isset($input['username']) || !isset($input['password'])) {
//        echo json_encode(["error" => "Faltan datos"]);
//        exit;
//    }
//
//    $username = $input['username'];
//    $password = $input['password'];
//
//    // Verificar las credenciales
//    if (isset($users[$username]) && $users[$username] === $password) {
//        // Si las credenciales son correctas, generar el token JWT
//        $token = JwtHandler::generateToken(["username" => $username]);
//
//        // Devolver el token como respuesta
//        echo json_encode(["token" => $token]);
//    } else {
//        echo json_encode(["error" => "Credenciales incorrectas"]);
//    }
//}
