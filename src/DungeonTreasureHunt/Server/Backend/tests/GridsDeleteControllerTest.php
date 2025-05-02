<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Backend\services\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;

class GridsDeleteControllerTest extends TestCase
{
    private AuthenticatedUserExtractor $authenticatedUserExtractor;
    private GridFileSystemRepository $gridRepository;
    private GridsDeleteController $sut;

    protected function setUp(): void
    {
        $this->authenticatedUserExtractor = $this->createMock(AuthenticatedUserExtractor::class);
        $this->gridRepository = $this->createMock(GridFileSystemRepository::class);

        $this->sut = new GridsDeleteController(
            $this->authenticatedUserExtractor,
            $this->gridRepository
        );
    }

    #[Test]
    public function it_should_delete_grid_successfully()
    {
        $username = "testuser";
        $gridId = "1";

        $this->authenticatedUserExtractor->method('extractUser')->willReturn(['username' => $username]);

        $this->gridRepository->method('getGrid')->willReturn(new GridItem(
            'Test Grid',
            [[0, 1], [1, 0]],
            $username
        ));

        $request = new Request(
            ['Authorization' => "Bearer valid_token"],
            ['id' => $gridId]
        );

        $response = $this->sut->__invoke($request);

        $expectedResponse = new JsonResponse(200,
            [
                "status" => "success",
            ]
        );
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $this->authenticatedUserExtractor
            ->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = new Request([], ['id' => 1]);

        $response = $this->sut->__invoke($request);


        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals([
            'error' => 'Invalid Token',
            'status' => 'error'
        ], $body);

    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $this->authenticatedUserExtractor
            ->method('extractUser')
            ->willThrowException(new InvalidTokenException('Invalid Token'));

        $request = new Request(['Authorization' => 'Bearer invalidtoken'], ['id' => 1]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(401, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(['error' => 'Invalid Token', 'status' => 'error'], $body);

    }

    #[Test]
    public function it_should_return_400_if_id_missing()
    {
        $username = "testuser";
        $this->authenticatedUserExtractor->method('extractUser')->willReturn(['username' => $username]);

        $request = new Request(['Authorization' => "Bearer valid_token"], []);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(400, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(['error' => 'ID no proporcionado', 'status' => 'error'], $body);

    }

    #[Test]
    public function it_should_return_404_if_grid_does_not_exist()
    {
        $username = "testuser";
        $gridId = "999";

        $this->authenticatedUserExtractor->method('extractUser')->willReturn(['username' => $username]);


        $this->gridRepository->method('getGrid')->willReturn(null);

        $request = new Request(['Authorization' => "Bearer valid_token"], ['id' => $gridId]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(404, $response->getStatus());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(['error' => 'Grid no encontrado', 'status' => 'error'], $body);

    }
}
