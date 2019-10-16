<?php

declare(strict_types=1);

use Kikopolis\App\Config\Config;
use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\Core\Container\Container;

/**
 * Define constant to prevent direct access to scripts
 */
define('_KIKOPOLIS', 1);

/**
 * The front controller, entry point from the web to the framework.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

// Require the application core
require_once '../Core/Core.php';

// Instantiate the dependency injection container
//$container = new Container();
View::addFunction('countDaysFromBirth', function ($dob) {
    $now = time();
    $your_date = strtotime($dob);
    $datediff = $now - $your_date;
    return round($datediff / (60 * 60 * 24));
});

View::addFunction('coinFlip', function () {
    return rand(0,1);
});

// Require the route dispatcher
require_once Config::getAppRoot() . '/Core/Route/RouteDispatcher.php';
