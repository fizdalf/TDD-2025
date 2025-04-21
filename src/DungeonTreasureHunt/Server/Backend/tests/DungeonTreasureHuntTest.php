<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\models\Tile;
use DungeonTreasureHunt\Backend\services\DungeonTreasureHunt;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once "DungeonTreasureHunt.php";

class DungeonTreasureHuntTest extends TestCase
{
    #[Test]
    public function it_should_return_false_when_the_grid_is_empty()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure([]));
    }

    #[Test]
    public function it_should_return_false_when_the_grid_has_an_empty_row_is_empty()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure([[]]));
    }

    #[Test]
    public function it_should_return_false_when_there_is_only_one_element_in_the_whole_grid()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player]
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_the_treasure_is_on_the_right_of_the_player()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Treasure]
            ]
        ));
    }

    #[Test]
    public function it_should_return_false_when_there_are_two_elements_and_no_treasure()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Wall]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Path]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Player]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Player]
            ]
        ));

    }

    #[Test]
    public function it_should_return_false_when_there_are_two_elements_and_no_player()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Treasure, Tile::Wall]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Treasure, Tile::Path]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Treasure, Tile::Treasure]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Wall]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Path]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Treasure]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Wall]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Path]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Treasure]
            ]
        ));

    }

    #[Test]
    public function it_should_return_false_when_there_is_wall_between_the_player_and_the_treasure_is_at_the_rigth()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Wall, Tile::Treasure]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Path, Tile::Wall, Tile::Treasure]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Path, Tile::Path, Tile::Wall, Tile::Treasure]
            ]
        ));
        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Path, Tile::Path, Tile::Path, Tile::Wall, Tile::Treasure]
            ]
        ));

        $this->assertFalse($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Path, Tile::Path, Tile::Path, Tile::Wall, Tile::Path, Tile::Treasure]
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_there_is_wall_behind_the_player_and_the_treasure_is_to_the_right()
    {
        $sut = new DungeonTreasureHunt();
        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Wall, Tile::Player, Tile::Treasure]
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_the_treasure_is_just_one_ti()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Treasure, Tile::Player, Tile::Path]
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_treasure_and_player_are_on_the_bottom_line()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Wall],
                [Tile::Player, Tile::Treasure]
            ]
        ));

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Wall, Tile::Wall],
                [Tile::Player, Tile::Path, Tile::Treasure]
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_treasure_or_the_player_are_on_the_bottom()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Player],
                [Tile::Treasure],
            ]
        ));

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Treasure],
                [Tile::Player],
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_treasure_is_two_tiles_below_and_one_tile_to_the_right_and_there_is_two_walls_on_the_right()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Player, Tile::Wall],
                [Tile::Path, Tile::Wall],
                [Tile::Path, Tile::Treasure],
            ]
        ));
    }

    #[Test]
    public function it_should_return_true_when_treasure_is_two_tiles_below_and_one_tile_to_the_right_while_the_player_its_at_the_second_position_in_the_first_row()
    {
        $sut = new DungeonTreasureHunt();

        $this->assertTrue($sut->canReachTreasure(
            [
                [Tile::Path, Tile::Player, Tile::Wall],
                [Tile::Path, Tile::Wall, Tile::Wall],
                [Tile::Path, Tile::Path, Tile::Treasure],
            ]
        ));
    }

}
