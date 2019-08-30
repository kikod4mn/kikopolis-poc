<?php

use Kikopolis\App\Config\Config as Config;
use Kikopolis\Core\Container;
use Kikopolis\Core\Route\Router;

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

$container = new Container();
// $posts = $container->get('App\Controllers\Http\Posts');
// $post = $container->get('App\Models\Post');
// var_dump($posts);
// var_dump($post);

/**
 * Cookie settings
 */

/**
 * PHP Settings Development
 */
ini_set('error_reporting', E_ALL);
set_error_handler('Kikopolis\Core\Error::errorHandler');
set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
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

$router->get('/', 'home.index', ['namespace' => 'Http']);
$router->get('home/index', 'home.index', ['namespace' => 'Http']);
$router->get('home/about', 'home.about', ['namespace' => 'Http']);
$router->get('home/faq', 'home.faq', ['namespace' => 'Http']);
$router->get('posts/view/{slug:\d+}', 'posts.view', ['namespace' => 'Http']);
$router->get('posts/show', 'posts.show.show.me.more.options', ['namespace' => 'Http']);

// var_dump($router->getRoutes());
// var_dump(Config::getUrlRoot());
// var_dump(Config::getAppRoot());

//Match the requested URL
$router->dispatch($_SERVER['QUERY_STRING']);