<?php

namespace DungeonTreasureHunt\Backend\services;

class JsonResponse extends Response
{
    public function __construct(int $statusCode = 200, mixed $data = null)
    {
        parent::__construct($statusCode);

        $this->setHeader("Content-Type", "application/json");

        if ($data != null) {
            $this->setJsonBody($data);
        }
    }

    public function setJsonBody(array $data): void
    {
        $this->setBody(json_encode($data));
    }

    public function withJson(array $data): self
    {
        $this->setJsonBody($data);
        return $this;
    }

    public static function success(array $data): self
    {
        return new self(200, $data);
    }

    public static function error(string $message, int $statusCode = 400): self
    {
        return new self($statusCode, [
            'status' => 'error',
            'error' => $message
        ]);
    }
}
