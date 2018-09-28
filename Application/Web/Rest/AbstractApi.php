<?php

namespace Application\Web\Rest;

use Application\Web\{
    Request,
    Response
};

abstract class AbstractApi implements ApiInterface {


    abstract public function get(Request $request, Response $response);

    abstract public function put(Request $request, Response $response);

    abstract public function post(Request $request, Response $response);

    abstract public function delete(Request $request, Response $response);

}
