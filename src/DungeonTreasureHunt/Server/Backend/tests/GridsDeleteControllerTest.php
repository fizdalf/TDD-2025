<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\models\GridItem;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsDeleteControllerTest extends TestCase
{
    private JWTUserExtractor $jwtUserExtractor;
    private GridFileSystemRepository $gridRepository;
    private GridsDeleteController $sut;

    protected function setUp(): void
    {
        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);
        $this->gridRepository = $this->createMock(GridFileSystemRepository::class);
        $this->sut = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepository,
        );
    }

    #[Test]
    public function it_should_delete_grid_successfully()
    {
        $username = "testuser";
        $gridId = "1";

        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

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

        $this->assertEquals(200, $response->getStatus());
        //TODO: assert the body too!
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        $request = new Request([], ['id' => 1]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(401, $response->getStatus());
        //TODO: assert the body too!
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        $this->jwtUserExtractor->method('extractUsername')->willReturn(null);

        $request = new Request(['Authorization' => 'Bearer invalidtoken'], ['id' => 1]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(401, $response->getStatus());
        //TODO: assert the body too!
    }

    #[Test]
    public function it_should_return_400_if_id_missing()
    {
        $username = "testuser";

        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

        $request = new Request(['Authorization' => "Bearer valid_token"], []);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(400, $response->getStatus());
        //TODO: assert the body too!
    }

    #[Test]
    public function it_should_return_404_if_grid_does_not_exist()
    {
        $username = "testuser";
        $gridId = "999";

        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

        $request = new Request(['Authorization' => "Bearer valid_token"], ['id' => $gridId]);

        $response = $this->sut->__invoke($request);

        $this->assertEquals(404, $response->getStatus());
        //TODO: assert the body too!
    }
}