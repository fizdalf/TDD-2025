<?php

namespace DungeonTreasureHunt\Backend\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// TODO: Qué podríamos hacer para mejorar el uso de ésta clase, y no depender de una librería externa en nuestros tests
class JwtHandler
{
    private static $SECRET_KEY = "secretKey";
    private static $ALGORITHM = "HS256";

    public static function generateToken(mixed $user, $expTime = 3600): string
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + $expTime,
            "user" => $user
        ];
        return JWT::encode($payload, self::$SECRET_KEY, self::$ALGORITHM);
    }

    public static function verifyToken($token): false|array
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$SECRET_KEY, self::$ALGORITHM));
            return (array)$decoded->user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verify(string $token): ?array
    {
        $result = self::verifyToken($token);
        return $result === false ? null : $result;
    }
}

