<?php
namespace pumast3r\api\dtos;

class UserDto {
    public int $id;
    public string $login;
    public string $email;
    public string $phone;
    public string | null $avatar;
    public string $role;

    public function __construct($user) {
        $decodeUser = json_decode($user, true);

        $this->id = $decodeUser['id'];
        $this->login = $decodeUser['login'];
        $this->email = $decodeUser['email'];
        $this->phone = $decodeUser['phone'];
        $this->avatar = $decodeUser['avatar'];
        $this->role = $decodeUser['role'];
    }

    public function getInfoUser(): string {
        return json_encode(array(
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'role' => $this->role,
        ));
    }

}