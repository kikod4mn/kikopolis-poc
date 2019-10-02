<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Controllers;

use Kikopolis\App\Framework\Controllers\BaseController;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * The controller to extend for all other controllers.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Controller extends BaseController
{
    /**
     * The route parameters bag from the URL.
     * @var array
     */
    protected static $route_params = [];

    /**
     * Set the route parameters.
     * @param array $route_params
     * @return void
     */
    public static function setRouteParams(array $route_params): void
    {
        static::$route_params = $route_params;
    }

    /**
     * Get the current route parameters.
     * @return array
     */
    public static function getRouteParams(): array
    {
        return static::$route_params;
    }
}