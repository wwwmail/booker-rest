<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Database\{
    Connection,
    UsersService
};
use Application\Helper\Text;

class AuthApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false'  ;
    const ID_FIELD = 'id';

    protected $service;

    public function __construct($dbparams)
    {
        $this->service = new UsersService(
                new Connection($dbparams));
    }

    /**
     * Get auth user information
     * @param Request $request
     * @param Response $response
     */
    public function get(Request $request, Response $response)
    {
        if ($this->isAuth() == true) {

            $user = $this->getAuthUser();
            if ($user->getIsAdmin() == 1) {
                $admin = true;
            } else {
                $admin = false;
            }
            $response->setData(['success' => self::_TRUE,
                'message' => Text::t('success_auth'),
                'admin' => $admin,
                'id' => $user->getid()]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['success' => self::_FALSE,
                'message' => Text::t('bad_auth')]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    /**
     * Logout user
     * @param Request $request
     * @param Response $response
     */
    public function put(Request $request, Response $response)
    {
        $user = $this->getAuthUser();

        $date = (new \DateTime());
        $expire = $date->modify('-' . STAY_LOGINING_TIME . ' minutes')->format('Y-m-d H:i:s');

        $user->setExpire($expire);

        if ($this->service->save($user)) {
            $response->setData(['success' => self::_TRUE,
                'message' => Text::t('success_logout'),
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    /**
     * Login user
     * @param Request $request
     * @param Response $response
     */
    public function post(Request $request, Response $response)
    {
        $reqData = $request->getData();

        $user = $this->service->fetchByEmail($reqData['email']);

        if (password_verify($reqData['password'], $user->getPassword())) {

            $token = bin2hex(random_bytes(16));
            $date = (new \DateTime());
            $expire = $date->modify('+' . STAY_LOGINING_TIME . ' minutes')->format('Y-m-d H:i:s');

            $user->setExpire($expire);
            $user->setToken($token);


            if ($this->service->save($user)) {
                $response->setData(['success' => self::_TRUE,
                    'auth' => $token,
                    'message' => Text::t('succes_logining'),
                ]);
                $response->setStatus(Request::STATUS_200);
            } else {
                $response->setData([self::ERROR]);
                $response->setStatus(Request::STATUS_500);
            }
        } else {
            $response->setData([self::ERROR,
                'message' => Text::t('false_logining'),
            ]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    /**
     * Not Allowed
     * @param Request $request
     * @param Response $response
     */
    public function delete(Request $request, Response $response)
    {
            $response->setStatus(Request::STATUS_405);  
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
     * Get auth headers
     * @return string|null
     */
    private function getAuthorizationHeader()
    {
        $headers = null;

        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } else if (isset($_SERVER['Authorization'])) {
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

    /**
     * Get auth user
     * @return array|false
     */
    public function getAuthUser()
    {
        return $this->service->fetchByToken($this->getBearerToken());
    }

    /**
     * Check is auth
     * @return boolean
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

}
