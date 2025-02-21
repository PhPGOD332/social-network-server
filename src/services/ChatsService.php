<?php
namespace pumast3r\api\services;

use Exception;
use Error;
use PDO;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;

class ChatsService {
    public static function getChats($userId) {
        try {
            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "SELECT c.id AS chat_id, c.created_at, c.created_at, cp.user_id FROM chat_participants cp JOIN chats c ON cp.chat_id = c.id WHERE cp.user_id = :user_id ORDER BY c.created_at DESC";
            $query = $pdo->prepare($sql);
            $query->execute(['user_id' => $userId]);
            $chats = $query->fetchAll(PDO::FETCH_ASSOC);

            $returnChats = array();

            foreach ($chats as $chat) {
                $sql = "SELECT * FROM messages WHERE chat_id = :chat_id ORDER BY created_at DESC LIMIT 1";
                $query = $pdo->prepare($sql);
                $query->execute(['chat_id' => $chat['chat_id']]);
                $lastMessage = $query->fetch(PDO::FETCH_ASSOC);

                $sql = "SELECT * FROM users WHERE id = :user_id";
                $query = $pdo->prepare($sql);
                $query->execute(['user_id' => $lastMessage['user_id']]);
                $sender = $query->fetch(PDO::FETCH_ASSOC);

                $chat = [$chat['chat_id'], $chat['created_by'], $chat['created_at'], 'sender' => $sender, 'lastMessage' => $lastMessage];

                array_push($returnChats, $chat);
            }

            return $returnChats;
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    }
}