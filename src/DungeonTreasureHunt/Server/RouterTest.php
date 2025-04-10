<?php

namespace DungeonTreasureHunt;

require_once 'Router.php';

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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
    public function it_()
    {

        $sut = new Router();

        $sut->register('/grid', 'POST', function () {
            return 'Login Controller';
        });

        $controller = $sut->getController('/login', 'GET');

        $this->assertNull($controller);
    }

    #[Test]
    public function it_should_match_route_with_parameter()
    {
        $sut = new Router();

        $sut->register('/grids/{id}','DELETE',function ($params) {
            return "Deleted ID: ". $params['id'];
        });

        [$controller, $params] = $sut->getController('/grids/69', 'DELETE');

        $this->assertNotNull($controller);

        $response = $controller($params);
        $this->assertEquals('Deleted ID: 69', $response);
    }
}