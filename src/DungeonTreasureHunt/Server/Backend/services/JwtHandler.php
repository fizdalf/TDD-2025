<?php

namespace DungeonTreasureHunt\Backend\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler implements TokenHandlerInterface
{
    private string $secretKey;
    private string $algorithm;

    public function __construct(?string $secretKey = null, ?string $algorithm = null)
    {
        $this->secretKey = $secretKey ?? $_ENV['JWT_SECRET_KEY'] ?? 'secretKey';
        $this->algorithm = $algorithm ?? $_ENV['JWT_ALGORITHM'] ?? 'HS256';
    }

    public function generateToken(mixed $user, int $expTime = 3600): string
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + $expTime,
            "user" => $user
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    private function verifyToken(string $token): array|false
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key($this->secretKey, $this->algorithm)
            );
            return (array)$decoded->user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verify(string $token): ?array
    {
        $result = $this->verifyToken($token);
        return $result === false ? null : $result;
    }
}

