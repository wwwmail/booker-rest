<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Entity\Events;
use Application\Database\{
    Connection,
    EventsService
};
use Application\Helper\AppHelper;

class EventsApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = 'true';
    const _FALSE = 'false';
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($registeredKeys, $dbparams, $tokenField = NULL)
    {
        parent::__construct($registeredKeys, $tokenField);
        $this->service = new EventsService(
                new Connection($dbparams));
        $this->helper  = new AppHelper('UsersService', $dbparams);
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
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function put(Request $request, Response $response)
    {
       $data = json_decode(file_get_contents('php://input'), true);
        
       $result =  $this->service->updateEvent($data['data'], $data['recursion']);
        
      
        if ($result === true) {
            $response->setData(['success' => self::_TRUE,'message' => 'success updated']);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['success' => self::_FALSE, 'message' => $result]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function post(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        $reqData = $request->getData();
        
        $user_id = $this->helper->getAuthUserId();
        
        $event = $this->service->createEvent($reqData,$user_id);

        if ($event === true) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'event created successfully'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['succes' => self::_FALSE,
                'message' => $event]);
            $response->setStatus(Request::STATUS_200);
        }
    }

    public function delete(Request $request, Response $response)
    {
      
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $result = $this->service->removeEvent($id, $data['recursion']);
        

       if ($result === true) {
            $response->setData(['success' => self::_TRUE,
                'message' => 'event delete successfully'
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['succes' => self::_FALSE,
                'message' => $result]);
            $response->setStatus(Request::STATUS_200);
        }
    }

}
