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

    public function withHeader(string $key, string $value): self
    {
        $this->setHeader($key, $value);
        return $this;
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


    public function withStatus(int $code): self
    {
        $this->setStatusCode($code);
        return $this;
    }

    public function withJson(array $data): self
    {
        $this->setHeader("Content-Type", "application/json");
        $this->setBody(json_encode($data));
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

    public function getBody(): string
    {
        return $this->body;
    }

    public static function success(array $data): self
    {
        return (new self(200))->withJson($data);
    }

    public static function error(string $message, int $statusCode = 400): self
    {
        return (new self($statusCode))->withJson(["error" => $message]);
    }
}
