<?php
namespace pumast3r\api\routers;

use Exception;
use Error;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\ChatsService;

function route($method, $urlData, $formData) {
    if ($method === 'POST' && $urlData[1] === 'messages' && isset($formData['chatId'])) {
        try {
            $chatId = $formData['chatId'];

            $messages = ChatsService::getAllMessages($chatId);

            echo json_encode($messages);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    } else if ($method === 'POST' && count($urlData) === 0) {
        try {
            $userId = $formData['userId'];

            $chats = ChatsService::getChats($userId);

            echo json_encode($chats);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}