<?php
declare(strict_types=1);

namespace DungeonTreasureHunt\Backend;

class Position implements \JsonSerializable
{

    public $x;
    public $y;

    public function __construct($x, $y,)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(){
        return $this->x;
    }

    public function getY(){
        return $this->y;
    }

    public function move(Direction $direction): Position
    {
        $movement = match ($direction) {
            Direction::Left => [-1, 0],
            Direction::Right => [1, 0],
            Direction::Up => [0, -1],
            Direction::Down => [0, 1],
        };

        [$dx, $dy] = $movement;

        return new self(
            $this->x + $dx, $this->y + $dy
        );
    }

    public function jsonSerialize(): mixed
    {

        return [
            "x" => $this->x,
            "y" => $this->y
        ];
    }
}