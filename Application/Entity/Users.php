<?php

namespace Application\Entity;

class Users extends Base {

    const TABLE_NAME = 'app_users';

    public $id = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    protected $token = '';
    protected $expire = '';
    protected $password = '';
    public $isAdmin;
    public $isActive;
    protected $mapping = [
        'id' => 'id',
        'email' => 'email',
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'token' => 'token',
        'expire' => 'expire',
        'password' => 'password',
        'is_admin' => 'isAdmin',
        'is_active' => 'isActive',
    ];

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName($name)
    {
        $this->first_name = (string)$name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName($name)
    {
        $this->last_name = (string)$name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = (string)$email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        if($password && !empty($password)){
            $this->password = (string)$password;
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken($token)
    {
        //bin2hex(random_bytes(16))
        $this->token = (string)$token;
    }

    public function getExpire(): string
    {
        return $this->expire;
    }

    public function setExpire($expire)
    {
        $this->expire = (string)$expire;
    }

    public function getIsAdmin():int
    {
        return $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        if ($isAdmin) {
            $this->isAdmin = (int)$isAdmin;
        }
    }
    
    public function getIsActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        if ($isActive) {
            $this->isActive = (int)$isActive;
        }
    }

}
