<?php

namespace DungeonTreasureHunt;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\controllers\LoginController;
use DungeonTreasureHunt\Backend\controllers\PlayController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JwtTokenGenerator;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Router;
use DungeonTreasureHunt\Backend\services\SimpleUserAuthenticator;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;

$router = new Router();

$jwtHandler = new JwtHandler();
$jwtUserExtractor = new JWTUserExtractor($jwtHandler);
$AuthenticatedUserExtractor = new AuthenticatedUserExtractor($jwtUserExtractor);
$explorer = new DungeonTreasureHuntExplorer();


$gridRepository = new GridFileSystemRepository();
$tokenGenerator = new JwtTokenGenerator($jwtHandler);
$userAuthenticator = new SimpleUserAuthenticator();

$router->register('/login', 'POST', new LoginController(
    $tokenGenerator,
    $userAuthenticator
));

$router->register('/play', 'POST', new PlayController(
    $explorer
));

$router->register('/grids', 'POST', new GridsPostController(
    $AuthenticatedUserExtractor,
    $gridRepository
));

$router->register('/grids', 'GET', new GridsGetController(
    $jwtUserExtractor,
    $gridRepository
));

$router->register('/grids/{id}', 'DELETE', new GridsDeleteController(
    $jwtUserExtractor,
    $gridRepository,
));