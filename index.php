<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Acces-Control-Request-Method, Access-Control-Request-Headers, Access-Control-Allow-Origin, Authorization');


header('Content-Type: application/json');
header('Accept-Language: en');
header('Accept-Charset: utf-8');

include __DIR__ . '/config/db.config.php';
include __DIR__ . '/config/app.config.php';



require __DIR__ . '/loader.php';

use Application\App;

$obj = new App($dbParams);


