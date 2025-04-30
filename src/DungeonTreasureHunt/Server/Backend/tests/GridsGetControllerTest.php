<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepositoryFactory;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsGetControllerTest extends TestCase
{
    private ResponseBuilder $responseBuilder;
    private GridRepositoryFactory $gridRepositoryFactory;
    private JWTUserExtractor $jwtUserExtractor;
    private GridFileSystemRepository $gridRepository;
    private string $username = "Test";

    protected function setUp(): void
    {
        $this->responseBuilder = $this->createMock(ResponseBuilder::class);

        $this->responseBuilder->method('success')->willReturnCallback(
            function($data = []) {
                return (new Response(200))->withJson(['status' => 'success', ...$data]);
            }
        );
        $this->responseBuilder->method('unauthorized')->willReturnCallback(
            function($message) {
                return (new Response(401))->withJson(['status' => 'error', 'error' => $message]);
            }
        );
        $this->responseBuilder->method('internalServerError')->willReturn(
            (new Response(500))->withJson(['status' => 'error', 'error' => 'Internal Server Error'])
        );

        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);

        $this->gridRepository = $this->createMock(GridFileSystemRepository::class);

        $this->gridRepositoryFactory = $this->createMock(GridRepositoryFactory::class);
        $this->gridRepositoryFactory->method('createForUser')->willReturn($this->gridRepository);

        $path = __DIR__ . "/../data/{$this->username}_gridSaved.txt";
        $grids = [["name" => "Grid 1"], ["name" => "Grid 2"]];
        file_put_contents($path, json_encode($grids));
    }

    #[Test]
    public function it_should_return_grids_when_authorized()
    {
        $this->jwtUserExtractor->method('extractUsername')->willReturn($this->username);

        $this->gridRepository->method('loadGrids')->willReturn([
            ["name" => "Grid 1"],
            ["name" => "Grid 2"]
        ]);

        $headers = ['Authorization' => 'Bearer valid_token'];
        $request = new Request($headers);

        $controller = new GridsGetController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertTrue($body['status'] === "success");
        $this->assertArrayHasKey('grids', $body);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_no_token(): void
    {
        $request = new Request();

        $controller = new GridsGetController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals("Token no proporcionado", $body["error"]);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_token_is_invalid(): void
    {
        $this->jwtUserExtractor->method('extractUsername')->willReturn(null);

        $request = new Request(["Authorization" => "Bearer tokenfalso"]);

        $controller = new GridsGetController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals("Token inválido o expirado", $body["error"]);
    }
}