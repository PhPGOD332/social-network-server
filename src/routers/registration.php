<?php

namespace pumast3r\api\routers;

use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\UserService;

function route($method, $urlData, $formData) {
//    $pass = password_hash($urlData[0], PASSWORD_DEFAULT);
//    echo json_encode(['pass' => $pass]);
	if ($method === "POST") {
		try {
			$returnData = UserService::registration($formData);

			setcookie('refreshToken', $returnUser['refreshToken'], [
				'expires' => time() + (86400 * 30),
				'path' => '/',
				'domain' => $_SERVER['SERVER_NAME'],
				'secure' => true,
				'httponly' => true,
				'samesite' => 'None'
			]);

			echo json_encode($returnData);
		} catch (\Exception $e) {
			ApiError::InternalServerError($e);
		}
	}
}