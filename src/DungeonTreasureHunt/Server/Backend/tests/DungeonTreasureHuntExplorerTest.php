<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\models\Direction;
use DungeonTreasureHunt\Backend\models\Position;
use DungeonTreasureHunt\Backend\models\PossibleMovement;
use DungeonTreasureHunt\Backend\models\Tile;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHuntExplorer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DungeonTreasureHuntExplorerTest extends TestCase
{
    #[Test]
    public function it_should_return_path_when_we_find_the_treasure()
    {
        $grid = [
            [Tile::Wall, Tile::Path, Tile::Path, Tile::Path, Tile::Treasure],
            [Tile::Player, Tile::Path, Tile::Wall, Tile::Wall, Tile::Wall],
            [Tile::Wall, Tile::Path, Tile::Wall, Tile::Wall, Tile::Wall],
            [Tile::Wall, Tile::Path, Tile::Path, Tile::Path, Tile::Wall],
        ];

        $sut = new DungeonTreasureHuntExplorer();
        $path = $sut->findPathToTreasure($grid);

        $this->assertEquals(
            [
                new PossibleMovement(new Position(0, 1), Direction::Right),
                new PossibleMovement(new Position(1, 1), Direction::Up),
                new PossibleMovement(new Position(1, 0), Direction::Right),
                new PossibleMovement(new Position(2, 0), Direction::Right),
                new PossibleMovement(new Position(3, 0), Direction::Right),
            ],
            $path
        );


    }

    #[Test]
    public function it_should_return_path_when_we_find_treasure()
    {

        $grid = [
            [Tile::Player, Tile::Path, Tile::Path],
            [Tile::Path, Tile::Wall, Tile::Path],
            [Tile::Path, Tile::Wall, Tile::Treasure],
        ];

        $sut = new DungeonTreasureHuntExplorer();

        $path = $sut->findPathToTreasure($grid);

        $this->assertEquals(
            [
                new PossibleMovement(
                    new Position(0, 0),
                    Direction::Right
                ),
                new PossibleMovement(
                    new Position(1, 0),
                    Direction::Right
                ),
                new PossibleMovement(
                    new Position(2, 0),
                    Direction::Down
                ),
                new PossibleMovement(
                    new Position(2, 1),
                    Direction::Down
                ),
            ],
            $path
        );
    }

    #[Test]
    public function it_should_return_path_when_we_f()
    {
        $grid = [
            [Tile::Wall, Tile::Path, Tile::Path, Tile::Path, Tile::Wall],
            [Tile::Player, Tile::Path, Tile::Wall, Tile::Path, Tile::Wall],
            [Tile::Path, Tile::Wall, Tile::Wall, Tile::Path, Tile::Wall],
            [Tile::Path, Tile::Wall, Tile::Treasure, Tile::Path, Tile::Wall],
            [Tile::Path, Tile::Wall, Tile::Wall, Tile::Wall, Tile::Wall],
            [Tile::Path, Tile::Path, Tile::Path, Tile::Path, Tile::Wall],
        ];

        $sut = new DungeonTreasureHuntExplorer();

        $path = $sut->findPathToTreasure($grid);

        $this->assertEquals(
            [
                new PossibleMovement(
                    new Position(0, 1),
                    Direction::Right
                ),
                new PossibleMovement(
                    new Position(1, 1),
                    Direction::Up
                ),
                new PossibleMovement(
                    new Position(1, 0),
                    Direction::Right
                ),
                new PossibleMovement(
                    new Position(2, 0),
                    Direction::Right
                ),
                new PossibleMovement(
                    new Position(3, 0),
                    Direction::Down
                ),
                new PossibleMovement(
                    new Position(3, 1),
                    Direction::Down
                ),
                new PossibleMovement(
                    new Position(3, 2),
                    Direction::Down
                ),
                new PossibleMovement(
                    new Position(3, 3),
                    Direction::Left
                ),
            ],
            $path
        );
    }

    #[Test]
    public function it_should_return_path_when_we_()
    {
        $grid = [];

        $sut = new DungeonTreasureHuntExplorer();

        $path = $sut->findPathToTreasure($grid);

        $this->assertEquals([], $path);
    }
}