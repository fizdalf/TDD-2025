<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\exceptions\InvalidRequestException;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\GridRepository;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';
require_once __DIR__ . '/../controllers/GridsPostController.php';
require_once __DIR__ . '/../http/Request.php';
require_once __DIR__ . '/../services/GridRepository.php';
require_once __DIR__ . '/../services/JwtHandler.php';
require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../models/GridItem.php';
require_once __DIR__ . '/../exceptions/InvalidTokenException.php';
require_once __DIR__ . '/../exceptions/InvalidRequestException.php';

class GridsPostControllerTest extends TestCase
{
    #[Test]
    public function it_should_create_grid_successfully()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);
        $repo = $this->createMock(GridRepository::class);

        $repo->expects($this->once())->method('saveGrid')->with(
            new GridItem(
                "New Test Grid",
                [[0, 1], [1, 0]],
                $username
            )
        );

        $gridData = [
            "gridName" => "New Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = $this->createRequestWithMockedJsonParsing(
            ['Authorization' => "Bearer $token"],
            [],
            $gridData
        );

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()), $repo);
        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn(null);

        $repo = $this->createMock(GridRepository::class);
        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()), $repo);
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn('Bearer invalidtoken');

        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $jwtExtractor->method('extractUserInfo')->willThrowException(new InvalidTokenException('Invalid Token'));

        $repo = $this->createMock(GridRepository::class);
        $controller = new GridsPostController($jwtExtractor, $repo);
        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_400_if_missing_grid_data()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn("Bearer $token");
        $request->method('parseBodyAsJson')->willReturn(['incomplete' => 'data']);

        $jwtExtractor = $this->createMock(JWTUserExtractor::class);
        $jwtExtractor->method('extractUserInfo')->willReturn(["username" => $username]);

        $repo = $this->createMock(GridRepository::class);
        $controller = new GridsPostController($jwtExtractor, $repo);
        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_500_if_save_fails()
    {
        $username = "testuser";
        $token = JwtHandler::generateToken(["username" => $username]);

        $gridData = [
            "gridName" => "Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = $this->createRequestWithMockedJsonParsing(
            ['Authorization' => "Bearer $token"],
            [],
            $gridData
        );

        $repo = $this->createMock(GridRepository::class);
        $repo->expects($this->once())
            ->method('saveGrid')
            ->willThrowException(new Exception("Database error"));

        $controller = new GridsPostController(new JWTUserExtractor(new JwtHandler()), $repo);
        $response = $controller($request);

        $this->assertEquals(500, $response->getStatus());
        $this->assertEquals(json_encode(["error" => "No se pudo guardar"]), $response->getBody());
    }

    private function createRequestWithMockedJsonParsing(array $headers, array $params, array $bodyData): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getHeaders')
            ->willReturnCallback(function($header = null) use ($headers) {
                if ($header) {
                    return $headers[$header] ?? null;
                }
                return $headers;
            });

        $request->method('parseBodyAsJson')
            ->willReturn($bodyData);

        $request->method('getParams')
            ->willReturn($params);

        return $request;
    }
}