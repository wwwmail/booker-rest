<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response,
    Received
};
use Application\Entity\Users;
use Application\Database\{
    Connection,
    UsersService
};

class UsersApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new UsersService(
                new Connection($dbparams));
    }

    public function get(Request $request, Response $response)
    {
        $id = $response->getData() ?? 0;

        if ($id > 0) {
            $result = $this->service->
                    fetchById($id); 
        } else {

            $result = [];

            $fetch = $this->service->fetchAll();

            foreach ($fetch as $row) {
                $result[] = $row;
            }
        }
        if ($result) {
            $response->setData($result);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function put(Request $request, Response $response)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $cust = Users::arrayToEntity($data['data'], new Users());
        if ($newCust = $this->service->save($cust)) {
            $response->setData(['success' => self::SUCCESS_UPDATE]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function post(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        $reqData = $request->getData();

        if ($this->service->fetchByEmail($reqData['email'])) {

            $response->setData(['success' => self::_FALSE,
                'message' => 'user_exist']);
            $response->setStatus(Request::STATUS_200);

            return;
        }

        $random = openssl_random_pseudo_bytes(18);

        $salt = sprintf('$2y$%02d$%s', 13, // 2^n cost factor
                substr(strtr(base64_encode($random), '+', '.'), 0, 22)
        );

        $options = ['cost' => 13,
            'salt' => $salt];

        $hash = password_hash($reqData['password'], PASSWORD_BCRYPT, $options);

        $data['token'] = bin2hex(random_bytes(16));
        $data['password'] = $hash;
        $updateData = array_merge($reqData, $data);

        $updateCust = Users::arrayToEntity($updateData, new Users());

        //var_dump($updateCust);die;
        if ($this->service->save($updateCust)) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'user created successfully'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_500);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        $cust = $this->service->fetchById($id);
        if ($cust && $this->service->remove($cust)) {
            $response->setData(['success' => self::SUCCESS_DELETE,
                'id' => $id]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }

}
