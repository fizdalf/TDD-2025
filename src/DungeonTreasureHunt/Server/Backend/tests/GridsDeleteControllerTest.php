<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\controllers\GridsDeleteController;
use DungeonTreasureHunt\Backend\exceptions\GridNotFoundException;
use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\gridRepository\GridRepositoryFactory;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridsDeleteControllerTest extends TestCase
{
    private ResponseBuilder $responseBuilder;
    private GridRepositoryFactory $gridRepositoryFactory;
    private JWTUserExtractor $jwtUserExtractor;
    private GridFileSystemRepository $gridRepository;

    protected function setUp(): void
    {
        $this->responseBuilder = $this->createMock(ResponseBuilder::class);

        // Configuración del ResponseBuilder mock
        $this->responseBuilder->method('success')->willReturn(
            (new Response(200))->withJson(['status' => 'success'])
        );
        $this->responseBuilder->method('error')->willReturnCallback(
            function($message, $statusCode) {
                return (new Response($statusCode))->withJson(['status' => 'error', 'error' => $message]);
            }
        );

        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);

        // Mock del repositorio
        $this->gridRepository = $this->createMock(GridFileSystemRepository::class);

        // Mock de la factory de repositorios
        $this->gridRepositoryFactory = $this->createMock(GridRepositoryFactory::class);
        $this->gridRepositoryFactory->method('createForUser')->willReturn($this->gridRepository);
    }

    #[Test]
    public function it_should_delete_grid_successfully()
    {
        $username = "testuser";
        $gridId = "1";

        // Configurar el mock del extractor JWT
        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

        // Configurar el mock del repositorio
        $this->gridRepository->method('exists')->willReturn(true);
        $this->gridRepository->method('loadGrids')->willReturn([
            $gridId => ["gridName" => "Test Grid", "grid" => [[0, 1], [1, 0]]]
        ]);

        $request = new Request(
            ['Authorization' => "Bearer valid_token"],
            ['id' => $gridId]
        );

        $controller = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(200, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_missing()
    {
        // Configurar el mock del ResponseBuilder
        $this->responseBuilder->method('error')->willReturnCallback(
            function($message, $statusCode) {
                return (new Response($statusCode))->withJson(['status' => 'error', 'error' => $message]);
            }
        );

        $request = new Request([], ['id' => 1]);

        $controller = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_401_if_token_invalid()
    {
        // Configurar el mock del extractor JWT para devolver null (token inválido)
        $this->jwtUserExtractor->method('extractUsername')->willReturn(null);

        $request = new Request(['Authorization' => 'Bearer invalidtoken'], ['id' => 1]);

        $controller = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(401, $response->getStatus());
    }

    #[Test]
    public function it_should_return_400_if_id_missing()
    {
        $username = "testuser";

        // Configurar el mock del extractor JWT
        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

        $request = new Request(['Authorization' => "Bearer valid_token"], []);

        $controller = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(400, $response->getStatus());
    }

    #[Test]
    public function it_should_return_404_if_grid_does_not_exist()
    {
        $username = "testuser";
        $gridId = "999";

        // Configurar el mock del extractor JWT
        $this->jwtUserExtractor->method('extractUsername')->willReturn($username);

        // Configurar el mock del repositorio
        $this->gridRepository->method('exists')->willReturn(true);
        $this->gridRepository->method('loadGrids')->willReturn([]);

        $request = new Request(['Authorization' => "Bearer valid_token"], ['id' => $gridId]);

        $controller = new GridsDeleteController(
            $this->jwtUserExtractor,
            $this->gridRepositoryFactory,
            $this->responseBuilder
        );

        $response = $controller($request);

        $this->assertEquals(404, $response->getStatus());
    }
}