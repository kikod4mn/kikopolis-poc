<?php

use Kikopolis\App\Config\Config;

/**
 * Autoloading
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Session start
 */
session_start();

$approot = isset($approot) ? $approot : Config::getAppRoot();
$urlroot = isset($urlroot) ? $urlroot : Config::getUrlRoot();

// $route_compiler = new RouteCompiler();
// print_r($route_compiler->getBindings());

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
// set_error_handler('Kikopolis\Core\Error::errorHandler');
// set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
