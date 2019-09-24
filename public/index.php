<?php

use Kikopolis\Core\Aurora\View;
use Kikopolis\Core\Container;

/**
 * Define constant to prevent direct access to scripts
 */
define('_KIKOPOLIS', 1);

/**
 * The Front Controller
 * 
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

// Require the application core
require_once '../Core/Core.php';

// Instantiate the dependency injection container
$container = new Container();

View::addFunction('countDaysFromBirth', function ($dob) {
    $now = time();
    $your_date = strtotime($dob);
    $datediff = $now - $your_date;
    return round($datediff / (60 * 60 * 24));
});

// Require the route dispatcher
require_once $approot . '/Core/Route/RouteDispatcher.php';
