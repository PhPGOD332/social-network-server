<?php

namespace pumast3r\api\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\exceptions\ApiError;
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
            'nbf' => $now,
            'exp' => ($now + 60) * 30,
            'data' => json_decode($payload),
        ];

        $payloadRefresh = [
            'iss' => $_ENV['SERVER_DOMAIN'],
            'aud' => $_ENV['SERVER_DOMAIN'],
            'iat' => $now,
            'nbf' => $now,
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

    public static function validateAccessToken(string $accessToken) {
        global $JWT_ACCESS_KEY;

        try {
            JWT::decode($accessToken, new Key($JWT_ACCESS_KEY, 'HS256'));
            return $accessToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function validateRefreshToken(string $refreshToken) {
        global $JWT_REFRESH_KEY;

        try {
            JWT::decode($refreshToken, new Key($JWT_REFRESH_KEY, 'HS256'));
            return $refreshToken;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function saveToken(string $userID, string $refreshToken) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();
            $sql = 'SELECT * FROM tokens WHERE user_id = :userId';

            $query = $pdo->prepare($sql);
            $query->execute(['userId' => $userID]);
            $token = $query->fetch();

            if ($token) {
                $sql = 'UPDATE tokens SET refresh_token = :refreshToken WHERE user_id = :userId';

                $query = $pdo->prepare($sql);
                $query->execute(['refreshToken' => $refreshToken, 'userId' => $userID]);
								exit;
            }

          $sql = 'INSERT INTO tokens (refresh_token, user_id) VALUES(:refreshToken, :userId)';

            $query = $pdo->prepare($sql);
            $query->execute(['refreshToken' => $refreshToken, 'userId' => $userID]);

            return true;
        } catch (\Exception $e) {
            ApiError::InternalServerError($e);
        }
    }

    public static function findToken(string $refreshToken) {
        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();
        $sql = 'SELECT * FROM tokens WHERE refresh_token = :token';

        $query = $pdo->prepare($sql);
        $query->execute(['token' => $refreshToken]);

        $token = $query->fetch(\PDO::FETCH_ASSOC);

        return $token;
    }

		public static function removeToken(string $refreshToken) {
			try {
				$connection = new ConnectionClass();
				$pdo = $connection->getPDO();
				$sql = 'DELETE FROM tokens WHERE refresh_token = :token';

				$query = $pdo->prepare($sql);
				$query->execute(['token' => $refreshToken]);

				$token = $query->fetch();
				return $token;
			} catch (\Exception $e) {
				ApiError::InternalServerError($e);
			}
		}
}