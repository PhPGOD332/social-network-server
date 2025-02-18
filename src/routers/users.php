<?php
namespace pumast3r\api\routers;

use Error;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\FriendService;
use pumast3r\api\services\UserService;

function route($method, $urlData, $formData) {
    if ($method === 'POST' && $urlData[0] === 'find' && isset($formData['request'])) {
        try {
            $request = $formData['request'];

            $users = UserService::findUsers($request);

            echo json_encode($users);
        } catch (Exception $e) {
            ApiError::OptionalError($e);
        }
    } else if ($method === 'GET' && count($urlData) === 1) {
		try {
			$login = $urlData[0];
			$user = UserService::getUser(['login', $login]);

			if (!$user) {
				ApiError::BadRequest('Такого пользователя не существует');
			}

            $user['friends'] = UserService::getFriends($user['id']);

			$userDto = new UserDto(json_encode($user));
			$response = $userDto;
			echo json_encode($response);
		} catch (Exception $e) {
			ApiError::OptionalError($e);
		}
	} else if ($method === 'POST' && isset($formData)) {
		try {
//			$response = UserService::editUser($formData);

			echo json_encode($formData);

//			$userData = [
//				'user' => $formData
//			];

//			$returnData = UserService::editUser($userData);
		} catch (Exception $e) {
			ApiError::OptionalError($e);
		}
	}

//	header('HTTP/1.0 400 Bad Request');
//	echo json_encode(array(
//		'error' => 'Bad Request',
//	));
}