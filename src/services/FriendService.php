<?php

namespace pumast3r\api\services;

use Exception;
use Error;
use PDO;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;

class FriendService {
    public static function getFriends(string $userId) {
        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();

        $sql = 'SELECT * FROM friends_list WHERE user_id = :user_id';
        $listQuery = $pdo->prepare($sql);
        $listQuery->execute([':user_id' => $userId]);
        $friendsList = $listQuery->fetch();

        if ($friendsList !== false && count($friendsList) == 0) {
            return [];
        }

        $sql = 'SELECT * FROM friends WHERE friends_list_id = :id AND is_confirmed = true';
        $friendsQuery = $pdo->prepare($sql);
        $friendsQuery->execute([':id' => $friendsList['id']]);
        $friends = $friendsQuery->fetchAll(PDO::FETCH_ASSOC);

        $returnFriends = [];

        foreach ($friends as $key => $friend) {
            $sql = 'SELECT * FROM users WHERE id = :id';
            $userQuery = $pdo->prepare($sql);
            $userQuery->execute([':id' => $friend['friend_id']]);
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);
            $newFriend = new UserDto(json_encode($user));
            $returnFriends[$key] = $newFriend;
        }

        return $returnFriends;
    }

    public static function removeFriend($userId, $friendId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();
            $sql = "SELECT * FROM friends_list WHERE user_id = :user_id OR friend_id = :friend_id";
            $query = $pdo->prepare($sql);
            $query->execute([':user_id' => $userId, ':friend_id' => $friendId]);
            $friendsLists = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($friendsLists as $key => $list) {
                $sql = "DELETE FROM friends WHERE friends_list_id = :list_id AND friend_id = :friend_id";
                $query = $pdo->prepare($sql);
                $query->execute([':list_id' => $list['id'], ':friend_id' => $list['user_id'] === $friendId ? $userId : $friendId]);
            }

            $sql = "SELECT * FROM users WHERE id = :id";
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $friendId]);
            $removedFriend = $query->fetch();
            $removedFriend = new UserDto(json_encode($removedFriend));

            return $removedFriend;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }

    public static function addFriend($userId, $friendId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "SELECT * FROM friends_list WHERE user_id = :user_id OR user_id = :friend_id";
            $query = $pdo->prepare($sql);
            $query->execute(['user_id' => $userId, 'friend_id' => $friendId]);
            $friendsLists = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($friendsLists as $list) {
                $sql = "INSERT INTO friends SET friend_id = :friend_id, friends_list_id = :friends_list_id, is_confirmed = false";
                $query = $pdo->prepare($sql);
                $query->execute([':friend_id' => $list['user_id'] == $friendId ? $userId : $friendId, ':friends_list_id' => $list['id']]);
            }

            $sql = "SELECT * FROM users WHERE id = :id";
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $friendId]);

            $addedFriend = $query->fetch();
            $addedFriend = new UserDto(json_encode($addedFriend));

            return $addedFriend;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }

    public static function getRequests($userId) {
        if (!$userId) {
            ApiError::BadRequest("Некорректный пользователь");
        }

        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();
        $sql = 'SELECT * FROM friends_list WHERE user_id = :user_id';
        $query = $pdo->prepare($sql);
        $query->execute([':user_id' => $userId]);
        $friendsList = $query->fetch();

        $sql = 'SELECT * FROM friends WHERE friends_list_id = :id AND is_confirmed = false';
        $query = $pdo->prepare($sql);
        $query->execute([':id' => $friendsList['id']]);
        $requests = $query->fetchAll(PDO::FETCH_ASSOC);

        $ids = '';
        foreach ($requests as $request) {
            if ($request === $requests[count($requests) - 1]) {
                $ids .= $request['friend_id'];
            } else {
                $ids .= $request['friend_id'] . ', ';
            }
        }

        if ($ids === '') {
            return [];
        }

        $sql = "SELECT * FROM users WHERE id IN (".$ids.")";
        $query = $pdo->prepare($sql);
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        $returnRequests = [];
        foreach ($users as $key => $user) {
            $returnRequests[$key] = new UserDto(json_encode($user));
        }

        return $returnRequests;
    }

    public static function confirmRequest($userId, $friendId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "SELECT * FROM friends_list WHERE user_id = :userId OR user_id = :friendId";
            $query = $pdo->prepare($sql);
            $query->execute([':userId' => $userId, ':friendId' => $friendId]);
            $friendsLists = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($friendsLists as $key => $list) {
                $sql = "UPDATE friends SET is_confirmed = true WHERE friends_list_id = :list_id AND friend_id = :friend_id";
                $query = $pdo->prepare($sql);
                $query->execute([':list_id' => $list['id'], ':friend_id' => $list['user_id'] == $friendId ? $userId : $friendId]);
            }

            $sql = "SELECT * FROM users WHERE id = :id";
            $query = $pdo->prepare($sql);
            $query->execute([':id' => $friendId]);
            $newFriend = $query->fetch();
            $newFriend = new UserDto(json_encode($newFriend));

            return $newFriend;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }

    public static function rejectRequest($userId, $friendId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "SELECT * FROM friends_list WHERE user_id = :userId OR user_id = :friendId";
            $query = $pdo->prepare($sql);
            $query->execute([':userId' => $userId, ':friendId' => $friendId]);
            $friendsLists = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($friendsLists as $key => $list) {
                $sql = "DELETE FROM friends WHERE friends_list_id = :list_id AND friend_id = :friend_id";
                $query = $pdo->prepare($sql);
                $query->execute([':list_id' => $list['id'], ':friend_id' => $list['user_id'] == $friendId ? $userId : $friendId]);
            }

            $sql = "SELECT * FROM users WHERE id = :friend_id";
            $query = $pdo->prepare($sql);
            $query->execute(['friend_id' => $friendId]);

            $user = $query->fetch();
            $user = new UserDto(json_encode($user));

            return $user;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}