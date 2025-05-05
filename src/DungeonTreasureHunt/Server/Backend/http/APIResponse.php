<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\services\JsonResponse;
//TODO: test this!
class APIResponse extends JsonResponse
{

    private function __construct(int $statusCode = 200, mixed $data = null)
    {
        parent::__construct($statusCode, $data);
    }

    public static function success(?array $data = []): self
    {
        if ($data === null) {
            $data = [];
        }
        return new self(200, [
                "status" => "success",
                ...$data,
            ]
        );
    }

    public static function error(string $message, int $statusCode = 400): self
    {
        return new self($statusCode, [
            'status' => 'error',
            'error' => $message
        ]);
    }
}