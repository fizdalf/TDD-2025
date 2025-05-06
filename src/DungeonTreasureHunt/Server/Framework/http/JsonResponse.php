<?php

namespace DungeonTreasureHunt\Framework\http;

class JsonResponse extends Response
{
    public function __construct(int $statusCode = 200, mixed $data = null)
    {
        parent::__construct($statusCode);

        $this->setHeader("Content-Type", "application/json");

        if ($data != null) {
            $this->body = json_encode($data);
        }
    }
}