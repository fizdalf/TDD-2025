<?php

namespace DungeonTreasureHunt\Backend\services;

use DungeonTreasureHunt\Backend\http\Response;

interface ResponseBuilder
{
    public function success(array $data = []): Response;
    public function error(string $message, int $statusCode): Response;
    public function unauthorized(string $message): Response;
    public function internalServerError(): Response;
}