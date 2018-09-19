<?php

namespace Application;

use Application\Web\Rest\Server;

class App {

    public $class = '';

    public function __construct($dbParams, $key = '', $column = '')
    {
        list($a, $class) = explode('/', $_SERVER['REQUEST_URI'], 3);

        $class = ucfirst(parse_url($class, PHP_URL_PATH)) . 'Api';

        $this->class = "Application\Web\Rest\\$class";

        if (class_exists($this->class)) {
            $obj = new $this->class(['79e9b5211bbf2458a4085707ea378129'], $dbParams);
            $server = new Server($obj);
            $server->listen();
        }
    }

}
