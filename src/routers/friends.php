<?php

use pumast3r\api\services\FriendService;

function route($method, $urlData, $formData) {
    if ($method == 'POST' && $urlData[0] === 'add') {
        $userId = $formData['user']['_id'];
        $friendId = $formData['friend']['_id'];

        $result = FriendService::addFriend($userId, $friendId);
        $return = '';

        if (!$result) {
            $return = ['error' => 'При отправке заявки произошла ошибка'];
        } else {
            $return = ['success' => 'Заявка успешно отправлена'];
        }

        echo json_encode($return);
    }
}