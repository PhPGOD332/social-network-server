<?php
namespace pumast3r\api\dtos;

use DateTime;

class UserDto {
    public int $id;
    public string $login;
		public string $surname;
		public string $name;
		public string $patronymic;
		public string $dateBirth;
    public string | null $email;
    public string $phone;
    public string | null $avatar;
    public string $role;

    public function __construct(string $user) {
        $decodeUser = json_decode($user, true);

        $this->id = $decodeUser['id'];
        $this->login = $decodeUser['login'];
				$this->surname = $decodeUser['surname'];
				$this->name = $decodeUser['name'];
				$this->patronymic = $decodeUser['patronymic'];
				$this->dateBirth = $decodeUser['date_birth'];
        $this->email = $decodeUser['email'];
        $this->phone = $decodeUser['phone'];
        $this->avatar = $decodeUser['avatar'];
        $this->role = $decodeUser['role'];
    }

    public function getInfoUser(): array {
        return array(
            'id' => $this->id,
            'login' => $this->login,
						'surname' => $this->surname,
						'name' => $this->name,
						'patronymic' => $this->patronymic,
						'dateBirth' => $this->dateBirth,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'role' => $this->role,
        );
    }
}