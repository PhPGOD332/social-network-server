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
		$id = $urlData[0];
		$response = array();

		try {
			$connection = new ConnectionClass();
			$pdo = $connection->getPDO();
			$sql = 'SELECT * FROM users WHERE id = :id';


			$query = $pdo->prepare($sql);
			$query->execute(['id' => $id]);
			$user = $query->fetch();
            $user = json_encode($user);
			$response = new UserDto($user);

			echo $response->getInfoUser();
		} catch(Exception $e) {
			$response['error'] = $e->getMessage();

			echo json_encode($response);
		}
		return;
	} else if ($method == 'GET' && count($formData) === 1 && count($urlData) === 2) {
		try {
			$login = $formData['login'];
			$user = new UserDto(json_encode(UserService::getUser($login)));
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