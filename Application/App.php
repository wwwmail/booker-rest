<?php

namespace Application;

use Application\Web\Rest\Server;

class App {

    public $class = '';

    public function __construct($dbParams)
    {
        list($a, $class) = explode('/', $_SERVER['REQUEST_URI'], 3);

//  list($a, $b, $c ,$d, $class) = explode('/', $_SERVER['REQUEST_URI'], 6);

        $class = ucfirst(parse_url($class, PHP_URL_PATH)) . 'Api';

        $this->class = "Application\Web\Rest\\$class";

        if (class_exists($this->class)) {
            $obj = new $this->class($dbParams);
            $server = new Server($obj);
            $server->listen();
        }
    }

}
