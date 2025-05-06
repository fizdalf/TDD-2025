<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\gridRepository\GridFileSystemRepository;
use DungeonTreasureHunt\Backend\models\GridItem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GridFileSystemRepositoryTest extends TestCase
{
    private string $username = 'TestUser';
    private string $tempDir;
    private GridFileSystemRepository $repo;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/grid_repo_test';
        mkdir($this->tempDir, recursive: true);

        $this->repo = new GridFileSystemRepository($this->tempDir);
    }

    protected function tearDown(): void
    {
        $filePath = "{$this->tempDir}/{$this->username}_gridSaved.txt";
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
    }

    #[Test]
    public function it_should_return_empty_array_when_no_file_exists(): void
    {
        $grids = $this->repo->loadGrids($this->username);

        $this->assertIsArray($grids);
        $this->assertEmpty($grids);
    }

    #[Test]
    public function it_should_save_and_load_a_grid(): void
    {
        $gridItem = new GridItem('MyGrid', [[1, 0], [0, 1]], $this->username);
        $this->repo->saveGrid($gridItem);

        $grids = $this->repo->loadGrids($this->username);
        $this->assertCount(1, $grids);

        $firstGrid = array_values($grids)[0];
        $this->assertEquals('MyGrid', $firstGrid['gridName']);
        $this->assertEquals([[1, 0], [0, 1]], $firstGrid['grid']);
    }

    #[Test]
    public function it_should_delete_a_specific_grid(): void
    {
        $gridItem1 = new GridItem('Grid1', [[1]], $this->username);
        $this->repo->saveGrid($gridItem1);

        $gridItem2 = new GridItem('Grid2', [[0]], $this->username);
        $this->repo->saveGrid($gridItem2);

        $grids = $this->repo->loadGrids($this->username);
        $this->assertCount(2, $grids);

        $ids = array_keys($grids);
        $gridIdToDelete = $ids[0];

        $gridItemToDelete = new GridItem('Grid1', [[1]], $this->username, $gridIdToDelete);
        $this->repo->deleteGrid($gridItemToDelete);

        $gridsAfterDelete = $this->repo->loadGrids($this->username);
        $this->assertCount(1, $gridsAfterDelete);
    }

    #[Test]
    public function it_should_get_a_specific_grid(): void
    {
        $gridItem = new GridItem('GridX', [[1,1]], $this->username);
        $this->repo->saveGrid($gridItem);

        $grids = $this->repo->loadGrids($this->username);
        $id = array_key_first($grids);

        $result = $this->repo->getGrid($this->username, $id);

        $this->assertInstanceOf(GridItem::class, $result);
        $this->assertEquals('GridX', $result->name);
        $this->assertEquals([[1,1]], $result->grid);
    }
}
