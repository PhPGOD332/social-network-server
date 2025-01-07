<?php

namespace pumast3r\api\services;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;

class UserService {


    public static function refresh(string $refreshToken) {
        if (!$refreshToken) {
            ApiError::UnauthorizedError();
        }
        $userData = TokenService::validateRefreshToken($refreshToken);

        $tokenFromDb = TokenService::findToken($refreshToken);

        if (!$userData || !$tokenFromDb) {
            ApiError::UnauthorizedError();
        }

        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();
        $sql = 'SELECT * FROM users WHERE id = :id';

        $query = $pdo->prepare($sql);
        $query->execute(['id' => $tokenFromDb['user_id']]);

        $user = $query->fetch();
        $userDto = new UserDto(json_encode($user));
        $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));

        TokenService::saveToken($userDto->id, $tokens['refreshToken']);

        $returnUser = array(
            'accessToken' => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken'],
            'user' => $userDto,
        );

        return $returnUser;
    }
}