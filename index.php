<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Acces-Control-Request-Method, Access-Control-Request-Headers, Access-Control-Allow-Origin, Authorization');


header('Content-Type: application/json');
header('Accept-Language: en');
header('Accept-Charset: utf-8');
// simple REST server
include __DIR__ .  '/config/db.config.php';
include __DIR__ .  '/config/app.config.php';
// setup class autoloading
require __DIR__ . '/Application/Autoload/Loader.php';

// add current directory to the path
Application\Autoload\Loader::init(__DIR__ . '/');

use Application\App;

$obj = new App($dbParams);


