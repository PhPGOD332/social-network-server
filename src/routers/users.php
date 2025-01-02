<?php
namespace pumast3r\api\routers;

use Error;

use Exception;
use pumast3r\api\connect\ConnectionClass;
use pumast3r\api\types\TUser;

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
			$response = new TUser(json_encode($user));


			echo json_encode(array(
				'method' => 'GET',
				'id' => $id,
				'login' => $response->login,
				'email' => $response->email,
				'password' => $response->password
			));
		} catch(Exception $e) {
			$response['error'] = $e->getMessage();

			echo json_encode($response);
		}
		return;
	}

	header('HTTP/1.0 400 Bad Request');
	echo json_encode(array(
		'error' => 'Bad Request',
	));
}