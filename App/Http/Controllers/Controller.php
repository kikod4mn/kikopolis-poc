<?php

namespace Kikopolis\App\Http\Controllers;

use Kikopolis\App\Framework\Controllers\BaseController;

defined('_KIKOPOLIS') or die('No direct script access!');

class Controller extends BaseController
{
    protected static $route_params = [];

    public function __construct()
    {
        //
    }

    public static function setRouteParams($route_params)
    {
        static::$route_params = $route_params;
    }

    public static function getRouteParams()
    {
        return static::$route_params;
    }
}