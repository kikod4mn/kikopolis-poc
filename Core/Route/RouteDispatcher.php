<?php

use Kikopolis\App\Config\Config;
use Kikopolis\Core\Route\Router;

$router = new Router();
require_once Config::getAppRoot() . '/Core/web_routes.php';

//Match the requested URL
$router->dispatch($_SERVER['QUERY_STRING']);
