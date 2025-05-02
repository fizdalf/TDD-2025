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
$responseBuilder = new JsonResponseBuilderAdapter();
$explorer = new DungeonTreasureHuntExplorer();

$defaultUsername = "";
$gridRepository = (new GridRepositoryFactoryImpl())->createForUser($defaultUsername);
$tokenGenerator = new JwtTokenGenerator($jwtHandler);
$userAuthenticator = new SimpleUserAuthenticator();

$userGridRepository = new GridFileSystemRepository("");

$router->register('/login', 'POST', new LoginController(
    $responseBuilder,
    $tokenGenerator,
    $userAuthenticator
));

$router->register('/play', 'POST', new PlayController(
    $explorer
));

$router->register('/grids', 'POST', new GridsPostController(
    $AuthenticatedUserExtractor,
    $userGridRepository,
    $responseBuilder
));

$router->register('/grids', 'GET', new GridsGetController(
    $jwtUserExtractor,
    $gridRepository,
    $responseBuilder
));

$router->register('/grids/{id}', 'DELETE', new GridsDeleteController(
    $jwtUserExtractor,
    $gridRepository,
));