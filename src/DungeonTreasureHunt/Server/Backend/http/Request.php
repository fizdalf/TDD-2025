<?php

namespace DungeonTreasureHunt\Backend\http;

class Request
{
    private array $headers;
    private array $params;
    private array $body;

    public function __construct(array $headers = [], array $params = [])
    {
        $this->headers = $headers;
        $this->params = $params;

        $input = file_get_contents("php://input");
        $this->body = json_decode($input, true) ?? [];
    }

    public function getHeaders(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getParams(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}