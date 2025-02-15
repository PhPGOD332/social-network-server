<?php

namespace pumast3r\api\services;

use Exception;
use PDO;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;

class UserService {

    public static function getFriends(string $userId) {
        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();

        $sql = 'SELECT * FROM friends_list WHERE user_id = :user_id';
        $listQuery = $pdo->prepare($sql);
        $listQuery->execute([':user_id' => $userId]);
        $friendsList = $listQuery->fetch();

        if (count($friendsList) == 0) {
            ApiError::BadRequest('Друзья отсутствую');
        }

        $sql = 'SELECT * FROM friends WHERE friends_list_id = :id';
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

    public static function refresh(string $refreshToken) {
        if (!$refreshToken) {
            ApiError::UnauthorizedError();
        }
        $userData = TokenService::validateRefreshToken($refreshToken);

        $tokenFromDb = TokenService::findToken($refreshToken);

        if (!$userData || !$tokenFromDb) {
            ApiError::UnauthorizedError();
        }

        $user = self::getUser(['id', $tokenFromDb['user_id']]);

        $user['friends'] = UserService::getFriends($user['id']);

        $userDto = new UserDto(json_encode($user));
        $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));

        TokenService::saveToken($userDto->_id, $tokens['refreshToken']);

        $returnUser = array(
            'accessToken' => $tokens['accessToken'],
            'refreshToken' => $tokens['refreshToken'],
            'user' => $userDto,
        );

        return $returnUser;
    }

    public static function registration(array $regData) {
        try {
            $login = $regData['login'];
            $password = password_hash($regData['password'], PASSWORD_DEFAULT);
            $phone = $regData['phone'];
            $surname = $regData['surname'];
            $name = $regData['name'];
            $patronymic = $regData['patronymic'];
            $dateBirth = $regData['dateBirth'];

            $userInDb = self::getUser(['login', $login]);

            if ($login == $userInDb['login']) {
                ApiError::BadRequest('Такой логин уже существует');
            }

            if ($phone == $userInDb['phone']) {
                ApiError::BadRequest('Такой телефон уже существует');
            }

            $connection = new ConnectionClass();
            $pdo = $connection->getPDO();

            $sql = "INSERT INTO users (login, password, surname, name, patronymic, date_birth, phone, role) VALUES(:login, :password, :surname, :name, :patronymic, :dateBirth, :phone, 'USER')";
            $query = $pdo->prepare($sql);
            $query->execute(
                [
                    'login' => $login,
                    'password' => $password,
                    'surname' => $surname,
                    'name' => $name,
                    'patronymic' => $patronymic,
                    'dateBirth' => $dateBirth,
                    'phone' => $phone
                ]
            );

            $userID = $pdo->lastInsertId();

            $user = self::getUser(['id', $userID]);

            $user['friends'] = UserService::getFriends($user['id']);

            $userDto = new UserDto(json_encode($user));
            $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));
            TokenService::saveToken($userDto->_id, $tokens['refreshToken']);

            $returnUser = array(
                'accessToken' => $tokens['accessToken'],
                'refreshToken' => $tokens['refreshToken'],
                'user' => $userDto
            );

            return $returnUser;
        } catch (\Exception $e) {
            ApiError::InternalServerError($e);
        }
    }

    static public function getUser(array $data) {
        $typeData = $data[0];
        $dataValue = $data[1];

        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();

        $sql = "SELECT * FROM users WHERE {$typeData} = :value";
        $query = $pdo->prepare($sql);
        $query->execute(['value' => $dataValue]);

        $user = $query->fetch();

        $dateBirth = date('Y-m-d', strtotime($user['date_birth']));

        $user['date_birth'] = $dateBirth;

        return $user;
    }

    static public function editUser(array $data) {
        $name = $data['name'];
        $surname = $data['surname'];
        $patronymic = $data['patronymic'];
        $dateBirth = $data['dateBirth'];

        if ($data['file']) {
            $tmp_name = $_FILES[0];
            move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']);
        }

        $connection = new ConnectionClass();
        $pdo = $connection->getPDO();
        $sql = 'UPDATE users SET ';
    }
}