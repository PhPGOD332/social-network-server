<?php

namespace pumast3r\api\routers;

use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\FriendService;

function route($method, $urlData, $formData) {
    if ($method === 'POST' && $urlData[1] === 'requests') {
        try {
            $userId = $formData['userId'];

            $requests = FriendService::getRequests($userId);

            echo json_encode($requests);
        } catch (\Exception $e) {
            ApiError::OptionalError($e);
        }
    } else if ($method === 'POST' && $urlData[0] === 'requests' && $urlData[1] === 'confirm') {
        try {
            $userId = $formData['userId'];
            $friendId = $formData['friendId'];

            $result = FriendService::confirmRequest($userId, $friendId);

            echo json_encode($result);
        } catch (\Exception $e) {
            ApiError::OptionalError($e);
        }
    } else if ($method === 'POST' && $urlData[0] === 'requests' && $urlData[1] === 'reject') {
        try {
            $userId = $formData['userId'];
            $friendId = $formData['friendId'];

            $result = FriendService::rejectRequest($userId, $friendId);

            echo json_encode($result);
        } catch (\Exception $e) {
            ApiError::OptionalError($e);
        }
    } else if ($method === 'POST' && $urlData[0] === 'add') {
        try {
            $userId = $formData['userId'];
            $friendId = $formData['friendId'];

            $result = FriendService::addFriend($userId, $friendId);
            $return = '';

            if (!$result) {
                $return = ['error' => 'При отправке заявки произошла ошибка'];
            } else {
                $return = ['success' => 'Заявка успешно отправлена'];
            }

            echo json_encode($return);
        } catch (\Error $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else if ($method === 'POST' && isset($formData['userId'])) {
        try {
            $userId = $formData['userId'];

            $users = FriendService::getFriends($userId);
            if (count($users) == 0) {
                ApiError::BadRequest('Пользователей нет');
            }

            echo json_encode($users);
        } catch (\Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}