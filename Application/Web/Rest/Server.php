<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};

class Server {

    protected $api;

    public function __construct(ApiInterface $api)
    {
        $this->api = $api;
    }

    public function listen()
    {
        $request = new Request();
        $response = new Response($request);
        $getPost = $_REQUEST ?? array();
        $jsonData = json_decode(
                file_get_contents('php://input'), true);
        $jsonData = $jsonData ?? array();
        $request->setData(array_merge($getPost, $jsonData));
        $id = $request->getData()[$this->api::ID_FIELD] ?? NULL;
        switch (strtoupper($request->getMethod())) {
            case Request::METHOD_POST :
                $this->api->post($request, $response);
                break;
            case Request::METHOD_PUT :
                $this->api->put($request, $response);
                break;
            case Request::METHOD_DELETE :
                $this->api->delete($request, $response);
                break;
            case Request::METHOD_GET :
            default :
            // return all if no params
                $this->api->get($request, $response);
        }
        $this->processResponse($response);
        echo json_encode($response->getData());
    }

    protected function processResponse($response)
    {
        if ($response->getHeaders()) {
            foreach ($response->getHeaders() as $key => $value) {
                header($key . ': ' . $value, TRUE, $response->getStatus());
            }
        }
        header(Request::HEADER_CONTENT_TYPE
                . ': ' . Request::CONTENT_TYPE_JSON, TRUE);
        if ($response->getCookies()) {
            foreach ($response->getCookies() as $key => $value) {
                setcookie($key, $value);
            }
        }
    }

}
