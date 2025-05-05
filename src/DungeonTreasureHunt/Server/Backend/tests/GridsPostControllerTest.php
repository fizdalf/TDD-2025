<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsPostController;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\APIResponse;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Backend\services\JsonResponse;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsPostControllerTest extends TestCase
{
    private AuthenticatedUserExtractor $authenticatedUserExtractor;
    private GridRepository $gridRepository;

    protected function setUp(): void
    {
        $this->authenticatedUserExtractor = $this->createMock(AuthenticatedUserExtractor::class);
        $this->gridRepository = $this->createMock(GridRepository::class);
    }

    #[Test]
    public function it_should_create_grid_successfully()
    {
        $username = "testuser";

        $this->authenticatedUserExtractor->method('extractUser')->willReturn(["username" => $username]);

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
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);

        $expectedResponse = ApiResponse::success();
        $this->assertEquals($expectedResponse, $response);

    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $this->authenticatedUserExtractor->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn(null);

        $controller = new GridsPostController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);

        $expectedResponse = APIResponse::error('Token no proporcionado o mal formado', 401);

        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $this->authenticatedUserExtractor->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn('Bearer invalidtoken');

        $controller = new GridsPostController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);
        $expectedResponse = APIResponse::error('Token no proporcionado o mal formado', 401);

        $this->assertEquals($expectedResponse, $response);

    }

    #[Test]
    public function it_should_return_400_if_missing_grid_data()
    {
        $username = "testuser";

        $this->authenticatedUserExtractor->method('extractUser')->willReturn(["username" => $username]);

        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn("Bearer valid_token");
        $request->method('parseBodyAsJson')->willReturn(['incomplete' => 'data']);

        $controller = new GridsPostController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);

        $expectedResponse = APIResponse::error('Faltan datos');

        $this->assertEquals($expectedResponse, $response);

    }

    #[Test]
    public function it_should_return_500_if_save_fails()
    {
        $username = "testuser";

        $this->authenticatedUserExtractor->method('extractUser')->willReturn(["username" => $username]);

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
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);

        $expectedResponse = APIResponse::error('No se pudo guardaar', 500);

        $this->assertEquals($expectedResponse, $response);

    }

    private function createRequestWithMockedJsonParsing(array $headers, array $params, array $bodyData): Request
    {
        $request = $this->createMock(Request::class);
        $request->method('getHeaders')
            ->willReturnCallback(function ($header = null) use ($headers) {
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
