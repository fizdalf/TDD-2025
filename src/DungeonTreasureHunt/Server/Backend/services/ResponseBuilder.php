<?php

namespace DungeonTreasureHunt\Backend\services;

interface ResponseBuilder
{
    public function success(array $data = []): Response;
    public function error(string $message, int $statusCode): Response;
}