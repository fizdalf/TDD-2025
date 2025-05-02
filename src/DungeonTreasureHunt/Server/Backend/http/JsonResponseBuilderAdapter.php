<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\services\Response;

class JsonResponseBuilderAdapter implements \DungeonTreasureHunt\Backend\services\TokenGenerator
{
    public function success(array $data = []): Response
    {
        return JsonResponseBuilder::success($data);
    }

    public function error(string $message, int $statusCode): Response
    {
        return JsonResponseBuilder::error($message, $statusCode);
    }

}