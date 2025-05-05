<?php

namespace DungeonTreasureHunt\Backend\services;

use function json_encode;

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