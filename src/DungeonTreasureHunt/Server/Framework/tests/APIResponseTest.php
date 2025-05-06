<?php

namespace DungeonTreasureHunt\Framework\tests;


use DungeonTreasureHunt\Framework\http\APIResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class APIResponseTest extends TestCase
{
    #[Test]
    public function it_should_return_success_response_with_data()
    {
        $response = APIResponse::success(['key' => 'value']);


        $this->assertEquals(200, $response->getStatus());

        $expectedBody = json_encode([
            'status' => 'success',
            'key' => 'value'
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }

    #[Test]
    public function it_should_return_success_response_with_empty_data_when_null()
    {
        $response = APIResponse::success(null);

        $this->assertEquals(200, $response->getStatus());

        $expectedBody = json_encode([
            'status' => 'success'
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }

    #[Test]
    public function it_should_return_error_response_with_message_and_default_status()
    {
        $response = APIResponse::error('Something went wrong');

        $this->assertEquals(400, $response->getStatus());

        $expectedBody = json_encode([
            'status' => 'error',
            'error' => 'Something went wrong'
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }

    #[Test]
    public function it_should_return_error_response_with_custom_status()
    {
        $response = APIResponse::error('Unauthorized', 401);

        $this->assertEquals(401, $response->getStatus());

        $expectedBody = json_encode([
            'status' => 'error',
            'error' => 'Unauthorized'
        ]);

        $this->assertEquals($expectedBody, $response->getBody());
    }
}