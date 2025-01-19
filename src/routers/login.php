<?php

namespace pumast3r\api\routers;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\TokenService;
use pumast3r\api\helpers\DotenvClass;
use pumast3r\api\services\UserService;

DotenvClass::loadDotenv();
function route($method, $urlData, $formData) {
    if ($method == "POST" && count($formData) == 2) {
        $login = $formData['login'];
        $password = $formData['password'];

        try {
            $user = UserService::getUser(['login', $login]);

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
						TokenService::saveToken($userDto->_id, $tokens['refreshToken']);

            $returnUser = array(
                'accessToken' => $tokens['accessToken'],
                'refreshToken' => $tokens['refreshToken'],
                'user' => $userDto
            );

            setcookie('refreshToken', $returnUser['refreshToken'], [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]);

            echo json_encode($returnUser);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
//            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}