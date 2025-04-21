<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\models\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once "Queue.php";

class QueueTest extends TestCase
{
    #[Test]
    function it_should_return_true_when_create_queue_is_empty(){
        $sut = new Queue();

        $this->assertTrue($sut->isEmpty());
    }

    #[Test]
    function it_should_return_false_when_add_elements_in_queue(){
        $sut = new Queue();

        $sut->enqueue('1');

        $this->assertFalse($sut->isEmpty());
    }

    #[Test]
    function it_should_return_elements_in_order(){
        $sut = new Queue();

        $sut->enqueue('Primero');
        $sut->enqueue('Segundo');

        $this->assertEquals("Primero", $sut->dequeue());
        $this->assertEquals("Segundo", $sut->dequeue());
    }

    #[Test]
    function it_should_return_false_when_try_dequeue_element_in_queue_is_empty(){
        $sut = new Queue();

        $this->assertFalse($sut->dequeue());
    }
}