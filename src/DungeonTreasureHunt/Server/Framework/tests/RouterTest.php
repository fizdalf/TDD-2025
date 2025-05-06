<?php

namespace DungeonTreasureHunt\Framework\tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use DungeonTreasureHunt\Framework\services\Router;

class RouterTest extends TestCase
{
    #[Test]
    public function it_should_return_null_when_requesting_a_controller_for_a_non_registered_route_and_method()
    {

        $sut = new Router();

        $controller = $sut->getController('/login', 'POST');

        $this->assertNull($controller);
    }

    #[Test]
    public function it_should_return_the_correct_controller_when_requesting_it()
    {
        $sut = new Router();

        $controller = function () {
            return 'Login Controller';
        };
        $sut->register('/login', 'POST', $controller);

        $result = $sut->getController('/login', 'POST');

        $receivedController = $result[0] ?? null;

        $this->assertNotNull($receivedController);
        $this->assertEquals($controller, $receivedController);
    }

    #[Test]
    public function it_should_return_null_when_requesting_a_non_registered_method_for_a_registered_uri()
    {

        $sut = new Router();

        $sut->register('/login', 'POST', function () {
            return 'Login Controller';
        });

        $controller = $sut->getController('/login', 'GET');

        $this->assertNull($controller);
    }

    #[Test]
    public function it_should_match_route_with_parameter()
    {
        $sut = new Router();

        $controller = function ($params) {
            return "Deleted ID: " . $params['id'];
        };
        $sut->register('/grids/{id}', 'DELETE', $controller);

        [$receivedController, $params] = $sut->getController('/grids/69', 'DELETE');

        $this->assertEquals($controller, $receivedController);
        $this->assertEquals(["id" => "69"], $params);
    }

    #[Test]
    public function it_should_match_route_with_two_parameters()
    {
        $sut = new Router();

        $controller = function ($params) {
            return "Deleted ID: " . $params['id'];
        };
        $sut->register('/grids/{id}/{something}', 'DELETE', $controller);

        [$receivedController, $params] = $sut->getController('/grids/69/551', 'DELETE');

        $this->assertEquals($controller, $receivedController);
        $this->assertEquals(["id" => "69", "something" => 551], $params);
    }
}