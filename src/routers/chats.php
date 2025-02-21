<?php
namespace pumast3r\api\routers;

use Exception;
use Error;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\ChatsService;

function route($method, $urlData, $formData) {
    if ($method === 'POST' && count($urlData) === 0) {
        try {
            $userId = $formData['userId'];

            $messages = ChatsService::getChats($userId);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}