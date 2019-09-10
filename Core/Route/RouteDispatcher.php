<?php

use Kikopolis\Core\Route\Router;

$router = new Router();
require_once $approot . '/Core/web_routes.php';

//Match the requested URL
$router->dispatch($_SERVER['QUERY_STRING']);