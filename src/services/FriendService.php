<?php

namespace pumast3r\api\services;

use Exception;
use Error;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\exceptions\ApiError;

class FriendService {
    public static function addFriend($userId, $friendId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "SELECT * FROM friends_list WHERE user_id = :user_id";
            $query = $pdo->prepare($sql);
            $query->execute(['user_id' => $userId]);
            $friendsList = $query->fetch();

            $sql = "INSERT INTO friends SET friend_id = :friend_id, friends_list_id = :friends_list_id, is_confirmed = false";
            $query = $pdo->prepare($sql);
            $query->execute(['friend_id' => $friendId, 'friends_list_id' => $friendsList['id']]);
            return $query;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}