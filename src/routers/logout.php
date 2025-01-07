<?php

namespace pumast3r\api\routers;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\exceptions\ApiError;
use pumast3r\api\services\TokenService;

function route($method, $urlData, $formData) {
	if ($method == "POST") {
		try {
			$refreshToken = $formData['refreshData'];
			$token = TokenService::removeToken($refreshToken);
			echo json_encode(['token' => $token]);
		} catch (Exception $e) {
			ApiError::InternalServerError($e);
		}
	}
}