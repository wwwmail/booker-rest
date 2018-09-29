<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};
use Application\Entity\Rooms;
use Application\Database\{
    Connection,
    RoomsService
};
use Application\Helper\{
    Filter,
    AppHelper
};

class RoomsApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = true;
    const _FALSE = false;
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($dbparams)
    {
        $this->service = new RoomsService(
                new Connection($dbparams));
        $this->helper = new AppHelper('UsersService', $dbparams);
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
        if ($this->helper->isAuthAdmin()) {
            $data = $request->getData();
            $obj = Rooms::arrayToEntity($data['data'], new Rooms());
            if ($this->service->save($obj)) {
                $response->setData(['success' => self::_TRUE, 'message' => 'update successfuly']);
                $response->setStatus(Request::STATUS_200);
            } else {
                $response->setData([self::ERROR]);
                $response->setStatus(Request::STATUS_500);
            }
        } else {
            $response->setData([self::ERROR]);
            $response->setStatus(Request::STATUS_401);
        }
    }

    public function post(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;
        $reqData = $request->getData();

        $filter = Filter::check($reqData, array(
                    'name' => ['required' => true,],
        ));

        if ($filter->passed()) {

            $obj = Rooms::arrayToEntity($reqData, new Rooms());

            if ($obj && $this->service->save($obj)) {
                $response->setData(['success' => self::_TRUE,
                    'message' => 'created new room']);
                $response->setStatus(Request::STATUS_200);
            } else {
                $response->setData([self::ERROR_NOT_FOUND]);
                $response->setStatus(Request::STATUS_500);
            }
        } else {
            $response->setData(['success' => self::_FALSE,
                'message' => $filter->errors()]);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $id = $request->getDataByKey(self::ID_FIELD) ?? 0;

        $check = $this->service->checkEventsExist($id);

        if (!$check) {
            $obj = $this->service->fetchById($id);

            if ($obj && $this->service->remove($obj)) {
                $response->setData(['success' => self::SUCCESS_DELETE,
                    'id' => $id]);
                $response->setStatus(Request::STATUS_200);
            } else {
                $response->setData([self::ERROR_NOT_FOUND]);
                $response->setStatus(Request::STATUS_500);
            }
        } else {
            $response->setData(['success' => self::_FALSE,
                'message' => 'this room has events']);
            $response->setStatus(Request::STATUS_200);
        }
    }

}
