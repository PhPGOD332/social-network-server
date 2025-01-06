<?php

namespace pumast3r\api\services;

use Firebase\JWT\JWT;
use pumast3r\api\helpers\DotenvClass;

DotenvClass::loadDotenv();

global $JWT_ACCESS_KEY;
$JWT_ACCESS_KEY = $_ENV['JWT_ACCESS_KEY'];

global $JWT_REFRESH_KEY;
$JWT_REFRESH_KEY = $_ENV['JWT_REFRESH_KEY'];

class TokenService {
    public static function generateTokens(string $payload): array {
        global $JWT_ACCESS_KEY;
        global $JWT_REFRESH_KEY;
        $now = time();

        $payloadAccess = [
            'iss' => $_ENV['SERVER_DOMAIN'],
            'aud' => $_ENV['SERVER_DOMAIN'],
            'iat' => $now,
            'nbf' => $now + 10,
            'exp' => ($now + 60) * 30,
            'data' => json_decode($payload),
        ];

        $payloadRefresh = [
            'iss' => $_ENV['SERVER_DOMAIN'],
            'aud' => $_ENV['SERVER_DOMAIN'],
            'iat' => $now,
            'nbf' => $now + 10,
            'exp' => ($now + 60) * 60 * 24 * 30,
            'data' => json_decode($payload),
        ];

        $accessToken = JWT::encode($payloadAccess, $JWT_ACCESS_KEY, 'HS256');
        $refreshToken = JWT::encode($payloadRefresh, $JWT_REFRESH_KEY, 'HS256');

        return array(
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken
        );
    }
}