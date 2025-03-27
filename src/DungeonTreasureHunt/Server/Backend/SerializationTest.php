<?php


require_once 'Position.php';
require_once 'PossibleMovement.php';
require_once 'Direction.php';

use DungeonTreasureHunt\Backend\Direction;
use DungeonTreasureHunt\Backend\Position;
use DungeonTreasureHunt\Backend\PossibleMovement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SerializationTest extends TestCase
{
    #[Test]
    public function it_should_serialize_the_way_we_expect_it()
   {
        $data = [
            new PossibleMovement(
                new Position(0, 1),
                Direction::Right
            ),
            new PossibleMovement(
                new Position(1, 1),
                Direction::Down
            ),
            new PossibleMovement(
                new Position(1, 2),
                Direction::Down
            ),
            new PossibleMovement(
                new Position(1, 3),
                Direction::Right
            ),
            new PossibleMovement(
                new Position(2, 3),
                Direction::Right
            ),
            new PossibleMovement(
                new Position(3, 3),
                Direction::Up
            ),
            new PossibleMovement(
                new Position(3, 2),
                Direction::Up
            ),
        ];

        $json = json_encode($data);

        $expectedData = '[{"playerPosition":{"x":0,"y":1},"direction":"Right"},{"playerPosition":{"x":1,"y":1},"direction":"Down"},{"playerPosition":{"x":1,"y":2},"direction":"Down"},{"playerPosition":{"x":1,"y":3},"direction":"Right"},{"playerPosition":{"x":2,"y":3},"direction":"Right"},{"playerPosition":{"x":3,"y":3},"direction":"Up"},{"playerPosition":{"x":3,"y":2},"direction":"Up"}]';

        $this->assertEquals($expectedData, $json);

   }
}

