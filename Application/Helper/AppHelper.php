<?php

namespace Application\Helper;

use Application\Database\UsersService;
use Application\Database\CartService;
use Application\Database\Connection;

class AppHelper {

    private $service;
    private $class;

    public function __construct($class, $dbparams)
    {
        $this->class = $this->class = "Application\Database\\$class";
        $this->service = new $this->class(
                new Connection($dbparams));
        ;
    }

    /**
     * Get bearer token from headers
     * @return string|null
     */
    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Get authorization header
     * @return string|null
     */
    private function getAuthorizationHeader()
    {
        $headers = null;
         if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])){
                 $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
         }
        else if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * Get authorization user
     * @return array
     */
    public function getAuthUser()
    {
        return $this->service->fetchByToken($this->getBearerToken());
    }
    
    /**
     * Check if user is auth and is admin
     * @return bool
     */
    public function isAuthAdmin()
    {
        $authToken = $this->getBearerToken();
        $user = $this->service->fetchByToken($authToken);

        if (!empty($user) && time() < strtotime($user->getExpire()) && $user->isAdmin == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user is auth
     * @return bool
     */
    public function isAuth()
    {
        $authToken = $this->getBearerToken();
        $user = $this->service->fetchByToken($authToken);

        if (!empty($user) && time() < strtotime($user->getExpire())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get id auth user
     * @return int|false
     */
    public function getAuthUserId()
    {
        $authToken = $this->getBearerToken();
        $user = $this->service->fetchByToken($authToken);
        if ($user) {
            return $user->id;
        } else {
            return false;
        }
    }

}
