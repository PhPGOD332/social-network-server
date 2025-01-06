<?php

namespace pumast3r\api\exceptions;

use Exception;

class ApiError extends Exception {


    public static function UnauthorizedError() {
        return new ApiError('Пользователь не авторизован', 401);
    }

    public static function BadRequest(string $message) {
        return new ApiError($message, 400);
    }

    public static function InternalServerError(string $message) {
        return new ApiError($message, 500);
    }
}