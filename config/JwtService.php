<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    // Générer un JWT
    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 heure d'expiration

        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    // Valider un JWT
    public function validateToken(string $jwt): object
    {
        return JWT::decode($jwt, new Key($this->secret, 'HS256'));
    }
}
