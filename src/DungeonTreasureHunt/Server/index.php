<?php

use DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Router;

require_once __DIR__ . '/Backend/DungeonTreasureHuntExplorer.php';
require __DIR__ . '/Backend/JWT.php';

ini_set('html_errors', false);


$users = [
    "admin" => "1234",
    "user" => "abcd"
];


//$cosa = new Cosa();
//
//$cosa->register('/login', 'POST', function () {
//});
//
//$uri = $_SERVER['REQUEST_URI'];
//$method = $_SERVER['REQUEST_METHOD'];
//$controller = $cosa->getController($uri, $method);
//
//
//if(!$controller){
//    http_response_code(404);
//    exit;
//}
//
//$controller();
// DRY // Don't Repeat Yourself
// MACHO // Massive Compact Halo Objects


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

//
//interface DBAccess
//{
//    public function insert($tableName, $data, $newId = null): void;
//}
//
//class PostressDBAccess implements DBAccess {
//
//}
//
//class RegisterAnimalController
//{
//
//    function __construct(
//        private RegisterAnimalRequestValidator $validator,
//        private DBAccess                       $dbAccess
//    )
//    {
//
//    }
//
//    function registerAnimal(Request $request): Response
//    {
//
//        // validar request (tiene que existir: nombre del animal, fecha de nacimiento, especie, raza, nombre del dueño)
//        $errors = $this->validator->validate($request);
//
//        if ($errors) {
//            return ErrorResponse($errors->toJson());
//        }
//
//        $id = AnimalId::random();
//
//        $this->dbAccess->insert('animals',
//            [
//                "animalName" => $request->body['animalName'],
//                "dateOfBirth" => $request->body['dateOfBirth'],
//                ...
//            ],
//            $id->value()
//        );
//
//        return JsonResponse(
//            ["id" => $id->value()]
//        );
//
//    }
//}
//
//class RegisterAnimalRequestValidator
//{
//    public function validate(Request $request)
//    {
//        return [];
//    }
//}
//
//
//class TestDBAccess implements DBAccess
//{
//    private $calls = [];
//    public function insert($tableName, $data, $newId = null): void
//    {
//        $this->calls[] = [$tableName, $data, $newId];
//    }
//
//    public function getCalls()
//    {
//        return $this->calls;
//    }
//}
//
//
//function it_should_create_new_animal_if_request_validated()
//{
//
//    $dbAccess = new TestDBAccess();
//    $sut = new RegisterAnimalController(
//        new RegisterAnimalRequestValidator(),
//        $dbAccess
//    );
//
//
//    $reponse = $sut->registerAnimal(new Request("{animalName: 'manuel'}"));
//
//
//    $calls = $dbAccess->getCalls();
//
//    if(empty($calls)){
//        throw new Exception('The controller didn\'t call the database!');
//    }
//
//    $databaseCall = $calls[0];
//
//
//}


$routes = [
    "POST" => [
        "/login" => function () {
            header("Content-Type: application/json");
            $input = json_decode(file_get_contents("php://input"), true);
            if (!isset($input['username']) || !isset($input['password'])) {
                echo json_encode(["error" => "Faltan datos"]);
                return;
            }

            global $users;
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

            parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queryParams);
            $idToDelete = $queryParams['id'] ?? null;

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

$routers = new Router();

foreach ($routes as $method => $methodRoutes) {
    foreach ($methodRoutes as $uri => $controller) {
        $routers->register($uri, $method, $controller);
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = $routers->getController($uri, $method);

if (!$controller) {
    http_response_code(404);
    echo json_encode(["error" => "Ruta no encontrada"]);
    exit;
}

$controller();


//if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/login") {
//    $input = file_get_contents('php://input');
//    $data = json_decode($input, true);
//    header("Content-Type: application/json");
//    if (!isset($data['username']) || !isset($data['password'])) {
//        echo json_encode(["error" => "Faltan datos"]);
//        exit;
//    }
//
//    $username = $data['username'];
//    $password = $data['password'];
//
//    if (!isset($users[$username]) || $users[$username] !== $password) {
//        echo json_encode(["error" => "Credenciales incorrectas"]);
//        exit;
//    }
//
//    $token = JwtHandler::generateToken(["username" => $username]);
//    echo json_encode(["token" => $token]);
//    exit;
//}
//
//
//if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/play") {
//    $input = file_get_contents('php://input');
//    $data = json_decode($input, true);
//    header("Content-Type: application/json");
//    if (!$data) {
//        error_log("Error al decodificar el JSON recibido: " . $input);
//        echo json_encode(['error' => 'No se pudo procesar el grid']);
//        exit;
//    }
//
//    $explorer = new \DungeonTreasureHunt\Backend\DungeonTreasureHuntExplorer();
//    $path = $explorer->findPathToTreasure($data);
//    echo json_encode($path);
//    exit;
//}
//
//
//if ($method === "POST" && $_SERVER['REQUEST_URI'] === "/grids") {
//    $input = file_get_contents('php://input');
//    $data = json_decode($input, true);
//    header("Content-Type: application/json");
//    $headers = getallheaders();
//
//    error_log("Input recibido: " . $input);
//    if (!isset($headers['Authorization'])) {
//        echo json_encode(["error" => "Token no proporcionado"]);
//        exit;
//    }
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        echo json_encode(["error" => "Token inválido o expirado"]);
//        exit;
//    }
//
//
//    $data = json_decode($input, true);
//    if (!isset($data['gridName']) || !isset($data['grid'])) {
//        echo json_encode(["error" => "Grid o nombre del grid no proporcionado"]);
//        exit;
//    }
//
//    $gridName = $data['gridName'];
//    $grid = $data['grid'];
//
//    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";
//
//    $storedGrids = [];
//    $newId = 1;
//
//    if (file_exists($path)) {
//        $storedGrids = json_decode(file_get_contents($path), true);
//
//        $maxId = max([0, ...array_keys($storedGrids)]);
//        $newId = $maxId + 1;
//    }
//
//
//    $storedGrids[$newId] = [
//        'gridName' => $gridName,
//        'grid' => $grid
//    ];
//
//
//    if (file_put_contents($path, json_encode($storedGrids))) {
//        echo json_encode(["success" => true, "message" => "Grid guardado correctamente"]);
//    } else {
//        echo json_encode(["error" => "Error al guardar el grid en el archivo"]);
//    }
//    exit;
//}
//
//if ($method === "GET" && $_SERVER['REQUEST_URI'] === "/grids") {
//    header("Content-Type: application/json");
//    $headers = getallheaders();
//
//    if (!isset($headers['Authorization'])) {
//        echo json_encode(["error" => "Token no proporcionado"]);
//        exit;
//    }
//
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        echo json_encode(["error" => "Token inválido o expirado"]);
//        exit;
//    }
//
//
//    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";
//
//    if (!file_exists($path)) {
//        echo json_encode(["success" => true, "grids" => []]);
//        exit;
//    }
//
//
//    $fileContent = file_get_contents($path);
//
//    if (empty($fileContent)) {
//        echo json_encode(["success" => true, "grids" => []]);
//        exit;
//    }
//
//    $grids = json_decode($fileContent, true);
//
//    if ($grids === null) {
//        echo json_encode(["error" => "Error al leer el contenido del archivo"]);
//        exit;
//    }
//
//    echo json_encode(["success" => true, "grids" => $grids]);
//    exit;
//}
//
//if ($method === "DELETE" && isset($_GET['id'])) {
//    header("Content-Type: application/json");
//    $headers = getallheaders();
//
//    if (!isset($headers['Authorization'])) {
//        echo json_encode(["error" => "Token no proporcionado"]);
//        exit;
//    }
//
//    $token = str_replace("Bearer ", "", $headers['Authorization']);
//    $userData = JwtHandler::verifyToken($token);
//
//    if (!$userData) {
//        echo json_encode(["error" => "Token inválido o expirado"]);
//        exit;
//    }
//
//    $idToDelete = $_GET['id'];
//
//    $path = __DIR__ . "{$userData['username']}_gridSaved.txt";
//
//    if (!file_exists($path)) {
//        echo json_encode(["error" => "No se encontró el archivo"]);
//        exit;
//    }
//
//    $grids = json_decode(file_get_contents($path), true);
//
//    if (!isset($grids[$idToDelete])) {
//        echo json_encode(["error" => "Grid no encontrado"]);
//        exit;
//    }
//
//    unset($grids[$idToDelete]);
//
//    file_put_contents($path, json_encode($grids));
//    echo json_encode(["success" => true]);
//    exit;
//}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if (isset($routes[$method][$uri])) {
    $routes[$method][$uri]();
} else {
    http_response_code(404);
    echo json_encode(["error" => "Ruta no encontrada"]);
}