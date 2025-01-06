<?php

namespace pumast3r\api\routers;

use Exception;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\UserService;

function route($method, $urlData, $formData) {
    if ($method == "POST") {
        try {
            $refreshToken = $_COOKIE['refreshToken'];
            $userData = UserService::refresh($refreshToken);

            setcookie('refreshToken', $userData['refreshToken'], [
                'expires' => time() + (86400 * 30),
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'],
                'secure' => false,
                'httponly' => true,
                'samesite' => 'None'
            ]);

            echo json_encode($userData);
        } catch (Exception $e) {
            ApiError::InternalServerError($e);
        }
    }
}