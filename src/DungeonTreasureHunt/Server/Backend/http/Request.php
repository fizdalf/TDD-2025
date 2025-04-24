<?php

namespace DungeonTreasureHunt\Backend\http;

class Request
{
    private array $headers;
    private array $params;

    public function __construct(array $headers = [], array $params = [])
    {
        $this->headers = $headers;
        $this->params = $params;
    }

    public function getHeaders(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getParams(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}