<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\services\Response;
use DungeonTreasureHunt\Backend\services\ResponseBuilder;

class JsonResponseBuilderAdapter implements ResponseBuilder
{
    public function success(array $data = []): Response
    {
        return JsonResponseBuilder::success($data);
    }

    public function error(string $message, int $statusCode): Response
    {
        return JsonResponseBuilder::error($message, $statusCode);
    }

    public function unauthorized(string $message): Response
    {
        return JsonResponseBuilder::unauthorized($message);
    }

    public function internalServerError(): Response
    {
        return JsonResponseBuilder::internalServerError();
    }
}