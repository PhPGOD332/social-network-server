<?php
namespace pumast3r\api\types;

class TUser {
	public int $id;
	public string $login;
	public string $password;
	public string $email;

	public function __construct($user) {
		$decodeUser = json_decode($user, true);

		$this->id = $decodeUser['id'];
		$this->login = $decodeUser['login'];
		$this->password = $decodeUser['password'];
		$this->email = $decodeUser['email'];
	}
}