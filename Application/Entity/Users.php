<?php

namespace Application\Entity;

class Users extends Base {

    const TABLE_NAME = 'app_users';

    public $id;
    public $email;
    public $first_name;
    public $last_name;
    protected $token;
    protected $expire;
    protected $password;
    public $isAdmin;
    public $isActive;

    /**
     * @var array mapping from key  to column (values).
     */
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

    /**
     * Get firstName
     * @return string 
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * Set firstname
     * @param string $name
     */
    public function setFirstName($name)
    {
        $this->first_name = (string) $name;
    }

    /**
     * Get lastName
     * @return string 
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * Set lastname
     * @param string $name
     */
    public function setLastName($name)
    {
        $this->last_name = (string) $name;
    }

    /**
     * Get email
     * @return string 
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
    }

    /**
     * Get password
     * @return string 
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set password
     * @param string $password
     */
    public function setPassword($password)
    {
        if ($password && !empty($password)) {
            $this->password = (string) $password;
        }
    }

    /**
     * Get token
     * @return string 
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Set token
     * @param string $token
     */
    public function setToken($token)
    {
        //bin2hex(random_bytes(16))
        $this->token = (string) $token;
    }

    /**
     * Get expire
     * @return string 
     */
    public function getExpire(): string
    {
        return $this->expire;
    }

    /**
     * Set expire
     * @param string $expire
     */
    public function setExpire($expire)
    {
        $this->expire = (string) $expire;
    }

    /**
     * Get isAdmin
     * @return int 
     */
    public function getIsAdmin(): int
    {
        return $this->isAdmin;
    }

    /**
     * Set isAdmin
     * @param string $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = (int) $isAdmin;
    }

    /**
     * Get IsActive
     * @return int 
     */
    public function getIsActive(): int
    {
        return $this->isActive;
    }

    /**
     * Set IsActive
     * @param string $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (int) $isActive;
    }

}
