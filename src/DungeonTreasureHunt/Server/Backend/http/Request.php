<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\models\GridItem;

class Request
{
    private array $headers;
    private array $params;
    private ?string $body;

    public function __construct(array $headers = [], array $params = [], string $body = "")
    {
        $this->headers = $headers;
        $this->params = $params;
        $this->body = $body;
    }

    public function getHeaders(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getParams(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    public function parseBodyAsJson(): array
    {
        return json_decode($this->body, true) ?? [];
    }
}