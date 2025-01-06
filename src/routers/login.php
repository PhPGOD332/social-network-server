<?php

namespace pumast3r\api\routers;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\TokenService;

function route($method, $urlData, $formData) {
    if ($method == "POST" && count($formData) == 2) {
        $login = $formData['login'];
        $password = $formData['password'];

        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = 'SELECT * FROM users WHERE login = :login';
            $query = $pdo->prepare($sql);
            $query->execute(['login' => $login]);

            $user = $query->fetch();

            if(!$user) {
                ApiError::BadRequest('Пользователь с таким логином не найден');
//                echo json_encode(['error' => 'Пользователь с таким логином не найден', 'code' => '400']);
            }

            $isPassEquals = password_verify($password, $user['password']);

            if (!$isPassEquals) {
                ApiError::BadRequest('Неверный пароль');
//                http_response_code(400);
//                echo "Неверный пароль";
            }

            $userDto = new UserDto(json_encode($user));
            $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));

            $returnUser = array(
                'accessToken' => $tokens['accessToken'],
                'refreshToken' => $tokens['refreshToken'],
                'user' => $userDto
            );

            setcookie('refreshToken', $returnUser['refreshToken'], time() + (86400 * 30), '/', $_ENV['SERVER_DOMAIN'], true, true);

            echo json_encode($returnUser);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
//            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}