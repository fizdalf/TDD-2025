<?php

namespace DungeonTreasureHunt\Framework\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHandler implements TokenGenerator, JwtVerifier
{
    private string $secretKey;
    private string $algorithm;

    public function __construct(?string $secretKey = null, ?string $algorithm = null)
    {
        $this->secretKey = $secretKey ?? $_ENV['JWT_SECRET_KEY'] ?? 'secretKey';
        $this->algorithm = $algorithm ?? $_ENV['JWT_ALGORITHM'] ?? 'HS256';
    }

    public function generateToken(mixed $payload, int $expTime = 3600): string
    {
        $tokenPayload = [
            "iat" => time(),
            "exp" => time() + $expTime,
            "user" => $payload
        ];

        return JWT::encode($tokenPayload, $this->secretKey, $this->algorithm);
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

