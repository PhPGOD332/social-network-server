<?php
namespace pumast3r\api\routers;

use Error;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\dtos\UserDto;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\UserService;

function route($method, $urlData, $formData) {

	if ($method === 'GET' && count($urlData) === 1) {
		try {
			$login = $urlData[0];
			$user = new UserDto(json_encode(UserService::getUser(['login', $login])));
			$response = array(
				'user' => $user
			);
			echo json_encode($response);
		} catch (Exception $e) {
			ApiError::OptionalError($e);
		}
	}

	header('HTTP/1.0 400 Bad Request');
	echo json_encode(array(
		'error' => 'Bad Request',
	));
}