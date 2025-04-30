<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsPostControllerTest extends TestCase
{
    private ResponseBuilder $responseBuilder;
    private JWTUserExtractor $jwtUserExtractor;
    private GridRepository $gridRepository;

    protected function setUp(): void
    {
        $this->responseBuilder = $this->createMock(ResponseBuilder::class);

        $this->responseBuilder->method('success')->willReturn(
            (new Response(200))->withJson(['status' => 'success'])
        );
        $this->responseBuilder->method('error')->willReturnCallback(
            function($message, $statusCode) {
                return (new Response($statusCode))->withJson(['status' => 'error', 'error' => $message]);
            }
        );
        $this->responseBuilder->method('unauthorized')->willReturnCallback(
            function($message) {
                return (new Response(401))->withJson(['status' => 'error', 'error' => $message]);
            }
        );

        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);
        $this->gridRepository = $this->createMock(GridRepository::class);
    }

    #[Test]
    public function it_should_create_grid_successfully()
    {
        $username = "testuser";

        $this->jwtUserExtractor->method('extractUserInfo')->willReturn(["username" => $username]);

        $this->gridRepository->expects($this->once())->method('saveGrid')->with(
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
            ['Authorization' => "Bearer valid_token"],
            [],
            $gridData
        );

        $controller = new GridsPostController(
            $this->jwtUserExtractor,
            $this->gridRepository,
            $this->responseBuilder
        );

        $response = $controller($request);
        $this->assertEquals(200, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn(null);

        $controller = new GridsPostController(
            $this->jwtUserExtractor,
            $this->gridRepository,
            $this->responseBuilder
        );

        $response = $controller($request);
        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $this->jwtUserExtractor->method('extractUserInfo')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn('Bearer invalidtoken');

        $controller = new GridsPostController(
            $this->jwtUserExtractor,
            $this->gridRepository,
            $this->responseBuilder
        );

        $response = $controller($request);
        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_400_if_missing_grid_data()
    {
        $username = "testuser";

        $this->jwtUserExtractor->method('extractUserInfo')->willReturn(["username" => $username]);

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn("Bearer valid_token");
        $request->method('parseBodyAsJson')->willReturn(['incomplete' => 'data']);

        $controller = new GridsPostController(
            $this->jwtUserExtractor,
            $this->gridRepository,
            $this->responseBuilder
        );

        $response = $controller($request);
        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_500_if_save_fails()
    {
        $username = "testuser";

        $this->jwtUserExtractor->method('extractUserInfo')->willReturn(["username" => $username]);

        $this->gridRepository->method('saveGrid')
            ->willThrowException(new Exception("Database error"));

        $gridData = [
            "gridName" => "Test Grid",
            "grid" => [[0, 1], [1, 0]]
        ];

        $request = $this->createRequestWithMockedJsonParsing(
            ['Authorization' => "Bearer valid_token"],
            [],
            $gridData
        );

        $controller = new GridsPostController(
            $this->jwtUserExtractor,
            $this->gridRepository,
            $this->responseBuilder
        );

        $response = $controller($request);
        $this->assertEquals(500, $response->getStatus());
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