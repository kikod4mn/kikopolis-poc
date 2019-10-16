<?php

declare(strict_types=1);

use Kikopolis\App\Config\Config;
use Kikopolis\Core\Route\Router;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Cookie
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

$router = new Router();
require_once Config::getAppRoot() . '/App/web_routes.php';
require_once Config::getAppRoot() . '/App/api_routes.php';

//Match the requested URL
$router->dispatch($_SERVER['QUERY_STRING']);
