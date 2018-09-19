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

    private function getAuthorizationHeader()
    {
        //var_dump($_SERVER); die;
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function getAuthUser()
    {
        return $this->service->fetchByToken($this->getBearerToken());
    }

    public function isAuth()
    {
        $authToken = $this->getBearerToken();
        //echo $authToken; die;
        $user = $this->service->fetchByToken($authToken);

//        var_dump($authToken);
//        var_dump($user); die;
        if (!empty($user) && time() < strtotime($user->getExpire())) {
            //echo 'good';
            return true;
        } else {
            // echo 'bad';
            return false;
        }
    }

    public function getAuthUserId()
    {
        $authToken = $this->getBearerToken();
        //echo $authToken; die;
        $user = $this->service->fetchByToken($authToken);

        if ($user) {
            return $user->id;
        } else {
            return false;
        }
    }

    public function arrayDiffRecursive($firstArray, $secondArray, $reverseKey = false)
    {
        $oldKey = 'old';
        $newKey = 'new';
        if ($reverseKey) {
            $oldKey = 'new';
            $newKey = 'old';
        }
        $difference = [];
        foreach ($firstArray as $firstKey => $firstValue) {
            if (is_array($firstValue)) {
                if (!array_key_exists($firstKey, $secondArray) || !is_array($secondArray[$firstKey])) {
                    $difference[$oldKey][$firstKey] = $firstValue;
                    $difference[$newKey][$firstKey] = '';
                } else {
                    $newDiff = $this->arrayDiffRecursive($firstValue, $secondArray[$firstKey], $reverseKey);
                    if (!empty($newDiff)) {
                        $difference[$oldKey][$firstKey] = $newDiff[$oldKey];
                        $difference[$newKey][$firstKey] = $newDiff[$newKey];
                    }
                }
            } else {
                if (!array_key_exists($firstKey, $secondArray) || $secondArray[$firstKey] != $firstValue) {
                    $difference[$oldKey][$firstKey] = $firstValue;
                    $difference[$newKey][$firstKey] = $secondArray[$firstKey];
                }
            }
        }
        return $difference;
    }

}
