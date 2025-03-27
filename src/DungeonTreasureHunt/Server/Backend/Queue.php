<?php

namespace DungeonTreasureHunt\Backend;

class Queue
{

    private array $elements = [];

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function enqueue(mixed $element): void
    {
        $this->elements[] = $element;
    }

    public function dequeue(): mixed
    {

        if ($this->isEmpty()){
            return false;
        }

        return array_shift($this->elements);
    }
}