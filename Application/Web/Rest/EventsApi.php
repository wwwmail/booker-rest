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
use Application\Helper\{AppHelper, Text};

class EventsApi extends AbstractApi {

    const ERROR = 'ERROR';
    const ERROR_NOT_FOUND = 'ERROR: Not Found';
    const _TRUE = true;
    const _FALSE = false;
    const ID_FIELD = 'id'; // field name of primary key

    protected $service;

    public function __construct($dbparams)
    {

        $this->service = new EventsService(
                new Connection($dbparams));
        $this->helper = new AppHelper('UsersService', $dbparams);
    }

    public function get(Request $request, Response $response)
    {
        if (method_exists($this->service, $request->getFilter()) && !empty($request->getFilterData())) {
            $filter = $request->getFilter();
            $result = [];
            $fetch = $this->service->$filter($request->getFilterData());
            foreach ($fetch as $row) {
                $result[] = $row;
            }
        } else {
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

        $result = $this->service->updateEvent($data['data'], $data['recursion']);


        if ($result === true) {
            $response->setData(['success' => self::_TRUE, 
                                'message' => Text::t('success_updated')]);
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

        $event = $this->service->createEvent($reqData, $user_id);

        if ($event === true) {
            $response->setData(['success' => self::_TRUE,
                'message' => Text::t('success_created')
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

        $data = $request->getData();

        $result = $this->service->removeEvent($id, $data['recursion']);


        if ($result === true) {
            $response->setData(['success' => self::_TRUE,
                'message' => Text::t('success_delete_event')
            ]);
            $response->setStatus(Request::STATUS_200);
        } else {
            $response->setData(['succes' => self::_FALSE,
                'message' => $result]);
            $response->setStatus(Request::STATUS_200);
        }
    }

}
