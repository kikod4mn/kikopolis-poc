<?php

use Kikopolis\Core\Router;

/**
 * Define constant to prevent direct access to scripts
 */
define('_KIKOPOLIS', 1);

/**
 * The Front Controller
 * Handles routing, sessions and loading of bootstraps
 * 
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

/**
 * Autoloading
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
// spl_autoload_register(function ($class) {
//     $root = dirname(__DIR__);
//     $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
//     if (is_readable($file)) {
//         require_once $root . '/' . str_replace('\\', '/', $class) . '.php';
//     }
// });

/**
 * Cookie settings
 */

/**
 * PHP Settings Development
 */
ini_set('error_reporting', E_ALL);
// set_error_handler('Core\Error::errorHandler');
// set_exception_handler('Core\Error::exceptionHandler');
ini_set("xdebug.var_display_max_children", -1);
ini_set("xdebug.var_display_max_data", -1);
ini_set("xdebug.var_display_max_depth", -1);

/**
 * PHP Settings Production
 */
// ini_set('error_reporting', E_ALL ^ E_DEPRECATED);
// set_error_handler('Core\Error::errorHandler');
// set_exception_handler('Core\Error::exceptionHandler');

/**
 * Session start
 */
session_start();

/**
 * Routing
 */
$router = new Router();

$router->add('GET', 'home/index', 'home.index');
$router->add('GET', 'home/about', 'home.about');
$router->add('POST', 'home/faq', 'home.faq');

// var_dump($router->getRoutes());

//Match the requested URL
$router->dispatch($_SERVER['QUERY_STRING']);