<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JwtService
{
    protected string $secretKey;
    protected string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secretKey = config('services.jwt.secret', config('app.key'));
    }

    public function generateToken(array $payload, int $expiryInHours = 24): string
    {
        $issuedAt = time();
        $expiry = $issuedAt + ($expiryInHours * 3600);

        $payload = array_merge([
            'iat' => $issuedAt,
            'exp' => $expiry,
        ], $payload);

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            Log::error('JWT validation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getUserIdFromToken(string $token): ?int
    {
        $payload = $this->validateToken($token);
        return $payload['user_id'] ?? null;
    }
}
