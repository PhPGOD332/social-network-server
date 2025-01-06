<?php

namespace pumast3r\api\routers;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\TokenService;

function route($method, $urlData, $formData) {
    if ($method == "POST" && count($formData) == 2) {
        $login = $urlData[0];
        $hashedPassword = password_hash($urlData[1], PASSWORD_DEFAULT);

        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = 'SELECT * FROM users WHERE login = :login';
            $query = $pdo->prepare($sql);
            $query->execute(['login' => $login]);

            $user = $query->fetch();

            if(!$user) {
                throw ApiError::BadRequest('Пользователь с таким логином не найден');
//                echo json_encode(['error' => 'Пользователь с таким логином не найден', 'code' => '400']);
            }

            $isPassEquals = strcmp($hashedPassword, password_verify($user->password, PASSWORD_DEFAULT));

            if (!$isPassEquals) {
                throw ApiError::BadRequest('Неверный пароль');
//                http_response_code(400);
//                echo "Неверный пароль";
            }

            $userDto = new UserDto($user);
            $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));

            $returnUser = array(
                'accessToken' => $tokens['accessToken'],
                'refreshToken' => $tokens['refreshToken'],
                'user' => json_encode($userDto)
            );

            setcookie('refreshToken', $returnUser['refreshToken'], time() + (86400 * 30), '/', $_ENV['SERVER_DOMAIN'], true, true);

            echo json_encode($returnUser);
        } catch (Exception $e) {
            throw ApiError::InternalServerError('Произошла ошибка');
//            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}