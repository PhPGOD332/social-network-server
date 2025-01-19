<?php

namespace pumast3r\api\services;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;

class UserService {

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

        $userDto = new UserDto(json_encode($user));
        $tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));

        TokenService::saveToken($userDto->id, $tokens['refreshToken']);

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

				$userDto = new UserDto(json_encode($user));
				$tokens = TokenService::generateTokens(json_encode($userDto->getInfoUser()));
				TokenService::saveToken($userDto->id, $tokens['refreshToken']);

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

			$dateBirth = date('d.m.Y', strtotime($user['date_birth']));

			$user['birth_date'] = $dateBirth;

			return $user;
		}
}