<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\services\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../services/Response.php';

class ResponseTest extends TestCase
{
    #[Test]
    public function it_should_initialize_with_defaults()
    {

        $sut = new Response();

        $this->assertEquals(200,$sut->statusCode);
        $this->arrayHasKey("Content-Type", $sut->headers);
        $this->assertEquals("application/json", $sut->headers["Content-Type"]);
        $this->assertNull($sut->body);
    }

    #[Test]
    public function it_should_allow_setting_headers()
    {
        $sut = new Response();

        $sut->setHeader("Test","value");

        $this->assertEquals("value", $sut->headers["Test"]);
    }

    #[Test]
    public function it_should_chain_withHeader()
    {
        $response = (new Response())->withHeader("X-Header", "header-value");

        $this->assertEquals("header-value", $response->headers["X-Header"]);
    }

    #[Test]
    public function it_should_set_status_code()
    {
        $response = new Response();
        $response->setStatusCode(404);

        $this->assertEquals(404, $response->statusCode);
    }

    #[Test]
    public function it_should_chain_with_status()
    {
        $response = (new Response())->withStatus(201);

        $this->assertEquals(201, $response->statusCode);
    }

    #[Test]
    public function it_should_set_plain_body()
    {
        $response = new Response();
        $response->setBody("Hello");

        $this->assertEquals("Hello", $response->body);
    }

    #[Test]
    public function it_should_set_json_body()
    {
        $response = new Response();
        $response->setJsonBody(["ok" => true]);

        $this->assertJsonStringEqualsJsonString(
            json_encode(["ok" => true]),
            $response->body
        );
        $this->assertEquals("application/json", $response->headers["Content-Type"]);
    }

    #[Test]
    public function it_should_chain_with_json()
    {
        $response = (new Response())->withJson(["success" => true]);

        $this->assertJsonStringEqualsJsonString(
            json_encode(["success" => true]),
            $response->body
        );
        $this->assertEquals("application/json", $response->headers["Content-Type"]);
    }
}