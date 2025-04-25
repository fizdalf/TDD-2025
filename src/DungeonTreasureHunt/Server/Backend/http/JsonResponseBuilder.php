<?php

namespace DungeonTreasureHunt\Backend\http;

use DungeonTreasureHunt\Backend\services\Response;

require_once __DIR__ . '/../services/Response.php';

class JsonResponseBuilder
{
    public static function success(array $data = [], int $statusCode = 200): Response
    {
        return (new Response())
            ->withStatus($statusCode)
            ->withHeader("Content-Type", "application/json")
            ->withJson([
                "status" => "success",
                ...$data
            ]);
    }

    public static function error(string $message, int $statusCode = 400): Response
    {
        return (new Response())
            ->withStatus($statusCode)
            ->withHeader("Content-Type", "application/json")
            ->withJson([
                "status" => "error",
                "error" => $message
            ]);
    }

    public static function unauthorized(string $message = "No autorizado"): Response
    {
        return self::error($message, 401);
    }

    public static function notFound(string $message = "No encontrado"): Response
    {
        return self::error($message, 404);
    }

    public static function badRequest(string $message = "Petición incorrecta"): Response
    {
        return self::error($message, 400);
    }

    public static function internalServerError(): Response
    {
        return self::error('Internal Server Error', 500);
    }
}
