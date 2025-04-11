<?php

namespace DungeonTreasureHunt\Backend;

class Response
{
    private array $headers = [];
    private int $statusCode = 200;
    private mixed $body;

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

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->body;
    }
}
