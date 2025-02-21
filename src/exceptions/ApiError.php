<?php

namespace pumast3r\api\exceptions;

use Exception;

class ApiError extends Exception {


    public static function UnauthorizedError() {
        http_response_code(401);
        echo json_encode(['error' => 'Пользователь не авторизован']);
        exit;
//        return new ApiError('Пользователь не авторизован', 401);
    }

    public static function BadRequest(string $message) {
        http_response_code(400);
		echo json_encode(['error' => $message]);
        exit;
//        return new ApiError($message, 400);
    }

    public static function InternalServerError(string $message) {
        http_response_code(500);
        echo json_encode(['error' => $message]);
        exit;
//        return new ApiError($message, 500);
    }

    public static function OptionalError(Exception $e) {
        http_response_code($e->getCode());
        echo json_encode(['error' => $e->getMessage()]);
        exit;
//        return new ApiError($e->getMessage(), $e->getCode());
    }
}