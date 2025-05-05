<?php

namespace DungeonTreasureHunt\Backend\http;

class Response
{
    private array $headers = [];
    private int $statusCode;
    protected mixed $body;

    public function __construct(int $statusCode = 200, mixed $body = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function withHeader(string $key, string $value): self
    {
        $this->setHeader($key, $value);
        return $this;
    }

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    protected function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function withStatus(int $code): self
    {
        $this->setStatusCode($code);
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->body;
    }

    public function getStatus(): int
    {
        return $this->statusCode;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
