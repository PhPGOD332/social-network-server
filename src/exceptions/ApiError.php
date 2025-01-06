<?php

namespace pumast3r\api\exceptions;

use Exception;

class ApiError extends Exception {


    public static function UnauthorizedError() {
        http_response_code(401);
        exit;
//        return new ApiError('Пользователь не авторизован', 401);
    }

    public static function BadRequest(string $message) {
        http_response_code(400);
        exit;
//        return new ApiError($message, 400);
    }

    public static function InternalServerError(string $message) {
        http_response_code(500);
        exit;
//        return new ApiError($message, 500);
    }

    public static function OptionalError(Exception $e) {
        http_response_code($e->getCode());
        exit;
//        return new ApiError($e->getMessage(), $e->getCode());
    }
}