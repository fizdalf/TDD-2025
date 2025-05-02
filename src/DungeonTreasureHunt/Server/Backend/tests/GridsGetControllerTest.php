<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\JsonResponseBuilderAdapter;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\models\UserGrids;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsGetControllerTest extends TestCase
{
    private GridRepository $gridRepository;
    private JWTUserExtractor $jwtUserExtractor;
    private string $username = "Test";

    protected function setUp(): void
    {
        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);

        $this->gridRepository = $this->createMock(GridRepository::class);
        $this->username = "Test";
    }


    #[Test]
    public function it_should_return_grids_when_authorized()
    {
        $this->jwtUserExtractor->method('extractUsername')->willReturn($this->username);

        $this->gridRepository->method('getAllGrids')->with($this->username)->willReturn(
            new UserGrids(...[
                new GridItem("Grid 1", [], $this->username, 1),
                new GridItem("Grid 2", [], $this->username, 2)
            ])
        );

        $headers = ['Authorization' => 'Bearer valid_token'];
        $request = new Request($headers);

        $controller = new GridsGetController(
            $this->jwtUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);


        $this->assertEquals(200, $response->getStatus());

        $body = json_decode($response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertEquals("success", $body['status']);
        $this->assertArrayHasKey('grids', $body);
        $this->assertCount(2, $body['grids']);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_no_token(): void
    {
        $request = new Request();

        $controller = new GridsGetController(
            $this->jwtUserExtractor,
            $this->gridRepository
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
            $this->gridRepository
        );

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $this->assertEquals("Token inválido o expirado", $body["error"]);
    }
}