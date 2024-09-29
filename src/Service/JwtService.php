<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException; // Importation pour gérer les exceptions

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
        try {
            return JWT::decode($jwt, new Key($this->secret, 'HS256'));
        } catch (ExpiredException $e) {
            throw new \Exception('Token has expired.', 401);
        } catch (\Exception $e) {
            throw new \Exception('Token is invalid.', 401);
        }
    }
}
