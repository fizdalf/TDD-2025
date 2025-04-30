<?php

namespace DungeonTreasureHunt;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepositoryFactoryImpl;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilderAdapter;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Router;

$router = new Router();

$jwtHandler = new JwtHandler();
$jwtUserExtractor = new JWTUserExtractor($jwtHandler);

$gridRepositoryFactory = new GridRepositoryFactoryImpl();
$responseBuilder = new JsonResponseBuilderAdapter();

$router->register('/login', 'POST', new LoginController());
$router->register('/play', 'POST', new PlayController());
$router->register('/grids', 'POST', new GridsPostController($jwtUserExtractor, new GridFileSystemRepository("")));
$router->register('/grids', 'GET', new GridsGetController(
    $jwtUserExtractor,
    $gridRepositoryFactory,
    $responseBuilder
));
$router->register('/grids/{id}', 'DELETE', new GridsDeleteController(
    $jwtUserExtractor,
    $gridRepositoryFactory,
    $responseBuilder
));