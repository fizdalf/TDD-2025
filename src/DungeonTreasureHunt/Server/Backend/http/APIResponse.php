<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\services\JsonResponse;
use DungeonTreasureHunt\Backend\services\Response;

class APIResponse extends JsonResponseBuilder
{
    public static function success(array $data = [], int $statusCode = 200): Response
    {
        return parent::success($data, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400): JsonResponse
    {
        return parent::error($message, $statusCode);
    }

    public static function unauthorized(string $message = "No autorizado"): Response
    {
        return parent::unauthorized($message);
    }

    public static function notFound(string $message = "No encontrado"): Response
    {
        return parent::notFound($message);
    }

    public static function badRequest(string $message = "Petición incorrecta"): Response
    {
        return parent::badRequest($message);
    }

    public static function internalServerError(): Response
    {
        return parent::internalServerError();
    }
}