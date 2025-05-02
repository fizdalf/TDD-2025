<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsGetController;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\gridRepository\GridRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\models\UserGrids;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsGetControllerTest extends TestCase
{
    private GridRepository $gridRepository;
    private AuthenticatedUserExtractor $authenticatedUserExtractor;
    private string $username = "Test";

    protected function setUp(): void
    {
        $this->authenticatedUserExtractor = $this->createMock(AuthenticatedUserExtractor::class);
        $this->gridRepository = $this->createMock(GridRepository::class);
    }

    #[Test]
    public function it_should_return_grids_when_authorized()
    {
        $this->authenticatedUserExtractor->method('extractUser')->willReturn(['username' => $this->username]);

        $this->gridRepository->method('getAllGrids')->with($this->username)->willReturn(
            new UserGrids(
                new GridItem("Grid 1", [], $this->username, 1),
                new GridItem("Grid 2", [], $this->username, 2)
            )
        );

        $headers = ['Authorization' => 'Bearer valid_token'];
        $request = new Request($headers);

        $controller = new GridsGetController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals([
            'status' => 'success',
            'grids' => [
                [
                    'grid' => [],
                    'id' => 1,
                    'name' => 'Grid 1'
                ],
                [
                    'grid' => [],
                    'id' => 2,
                    'name' => 'Grid 2'
                ]
            ]
        ], $body);
    }

    #[Test]
    public function test_it_returns_unauthorized_if_no_token(): void
    {

        $this->authenticatedUserExtractor
            ->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = new Request();

        $controller = new GridsGetController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(['error' => 'Invalid Token','status' => 'error'], $body);

    }

    #[Test]
    public function test_it_returns_unauthorized_if_token_is_invalid(): void
    {

        $this->authenticatedUserExtractor
            ->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = new Request(["Authorization" => "Bearer tokenfalso"]);

        $controller = new GridsGetController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );

        $response = $controller($request);
        $body = json_decode($response->getBody(), true);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(['error' => 'Invalid Token','status' => 'error'], $body);

    }
}
