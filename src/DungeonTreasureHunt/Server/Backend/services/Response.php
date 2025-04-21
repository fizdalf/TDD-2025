<?php

namespace DungeonTreasureHunt\Backend\services;

class Response
{
    private array $headers = [];
    private int $statusCode;
    private mixed $body;

    public function __construct(int $statusCode = 200, mixed $body = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->setHeader("Content-Type", "application/json");
    }

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setJsonBody(array $body): void
    {
        $this->body = json_encode($body);
        $this->setHeader("Content-Type", "application/json");
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->body;
    }
}
