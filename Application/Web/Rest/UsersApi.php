<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Entity\Users;
use Application\Database\{
    Connection,
    UsersService
};
use Application\Helper\AppHelper;

class UsersApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($dbparams)
    {
        $this->service = new UsersService(
                new Connection($dbparams));
        $this->helper = new AppHelper('UsersService', $dbparams);
    }

    public function get(Request $request, Response $response)
    {
        $id = $response->getData() ?? 0;

        if ($id > 0) {
           $result = $this->helper->isAuthAdmin() 
                    ? $this->service->fetchByIdAdmin($id) 
                    : $this->service->fetchById($id);
        } else {

            $result = [];

             $fetch = $this->helper->isAuthAdmin() 
                    ?  $this->service->fetchAllAdmin()
                    : $this->service->fetchAll();

            foreach ($fetch as $row) {
                $result[] = $row;
            }
        }
        if ($result) {
            $response->setData($result);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function put(Request $request, Response $response)
    {
        $data = $request->getData();

        $user = $this->service->fetchByIdAdmin($data['data']['id']);
       
        $obj = Users::arrayToEntity($data['data'], new Users());
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $hash = $this->service->createHashPassword($data['data']['password']);

            $obj->setPassword($hash);
        } else {
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
            $response->setData(['message' => $newUser['message']]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function delete(Request $request, Response $response)
    {

        $id = $response->getData() ?? 0;


        $check = $this->service->checkUsersEvent($id);
        if(!$check){
        $obj = $this->service->fetchByIdAdmin($id);
        
        $obj->setIsActive(0);
        if ($obj && $this->service->save($obj)) {
            $response->setData(['success' => true,
                'message' => 'succes delete user',
                'id' => $id]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData([self::ERROR_NOT_FOUND]);
            $response->setStatus(Request::STATUS_500);
        }
        
        }else{
            $response->setData(['success' => true,
                'message' => 'user have active events',
                'id' => $id]);
            $response->setStatus(Request::STATUS_200);
        }
    }

}
