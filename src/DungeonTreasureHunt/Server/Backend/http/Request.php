<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\models\GridItem;

require_once __DIR__ . '/../models/GridItem.php';


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
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }
}