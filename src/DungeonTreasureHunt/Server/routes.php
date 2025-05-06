<?php

namespace DungeonTreasureHunt;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Framework\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Framework\services\JwtHandler;
use DungeonTreasureHunt\Framework\services\JWTUserExtractor;
use DungeonTreasureHunt\Framework\services\Router;
use DungeonTreasureHunt\Framework\services\SimpleUserAuthenticator;

$router = new Router();

$jwtHandler = new JwtHandler();
$jwtUserExtractor = new JWTUserExtractor($jwtHandler);
$authenticatedUserExtractor = new AuthenticatedUserExtractor($jwtUserExtractor);
$explorer = new DungeonTreasureHuntExplorer();


$gridRepository = new GridFileSystemRepository();
$userAuthenticator = new SimpleUserAuthenticator();


$router->register('/login', 'POST', new LoginController(
    $jwtHandler,
    $userAuthenticator
));

$router->register('/play', 'POST', new PlayController(
    $explorer
));

$router->register('/grids', 'POST', new GridsPostController(
    $authenticatedUserExtractor,
    $gridRepository
));

$router->register('/grids', 'GET', new GridsGetController(
    $authenticatedUserExtractor,
    $gridRepository
));

$router->register('/grids/{id}', 'DELETE', new GridsDeleteController(
    $authenticatedUserExtractor,
    $gridRepository,
));