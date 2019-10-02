<?php

declare(strict_types=1);

/**
 * Kikopolis Core. Handle bootstrapping and configuring the environment.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

/**
 * Autoloading
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Session start
 */
session_start();

// $route_compiler = new RouteCompiler();
// print_r($route_compiler->getBindings());

/**
 * Cookie settings
 */

/**
 * PHP Settings Development
 */
ini_set('error_reporting', 'E_ALL');
set_error_handler('Kikopolis\Core\Error::errorHandler');
set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

/**
 * PHP Settings Production
 */
// ini_set('error_reporting', E_ALL ^ E_DEPRECATED);
// set_error_handler('Kikopolis\Core\Error::errorHandler');
// set_exception_handler('Kikopolis\Core\Error::exceptionHandler');
