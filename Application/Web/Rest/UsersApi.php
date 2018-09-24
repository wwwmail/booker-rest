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

        $user = $this->service->fetchById($data['data']['id']);
        //var_dump($data); die;
        $obj = Users::arrayToEntity($data['data'], new Users());
        if(isset($data['data']['password']) && !empty($data['data']['password'])){
            $hash = $this->service->createHashPassword($data['data']['password']);
        
            $obj->setPassword($hash);
        }else{
            $obj->setPassword($user->getPassword());
        }
        if ($newCust = $this->service->save($obj)) {
            $response->setData(['success' => self::_TRUE,
                                'message' => 'success updated']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function post(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        $reqData = $request->getData();
                
        $newUser = $this->service->createUser($reqData); 
        
       

        if ($newUser['success'] && $this->service->save($newUser['item'])) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'user created successfully'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['message' =>$newUser['message']]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function delete(Request $request, Response $response)
    {
        
        $id = $response->getData() ?? 0;

        
        
        $obj = $this->service->fetchById($id);
        if ($obj && $this->service->remove($obj)) {
            $response->setData(['success' => true,
                                'message' => 'succes delete user',
                'id' => $id]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
    }

}
