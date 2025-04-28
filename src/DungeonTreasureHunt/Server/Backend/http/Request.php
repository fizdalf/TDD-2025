<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\models\GridItem;

class Request
{
    private array $headers;
    private array $params;
    private array $body;
    private ?string $realBody;

    public function __construct(array $headers = [], array $params = [], string $body = "")


    {
        $this->headers = $headers;
        $this->params = $params;
        $this->realBody = $body;
    }

    public function getHeaders(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getParams(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
    /** @deprecated Use parseBodyAsJson instead! */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getRealBody(): string
    {
        return $this->realBody;
    }

    public function parseBodyAsJson(): array
    {
        return json_decode($this->realBody, true) ?? [];
    }
}