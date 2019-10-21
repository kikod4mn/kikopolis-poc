<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Controllers;

use Kikopolis\App\Framework\Controllers\BaseController;
use Kikopolis\App\Helpers\Str;

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
    protected $middleware = [];

    /**
     * Parameter bag from the GET array
     *
     * @var array
     */
    protected $params = [];

    protected function before() {
        //
    }

    protected function after() {
        //
    }

    final public function __construct($route_params = [])
    {
        if ($this->middleware !== []) {
            foreach ($this->middleware as $mware) {
                $this->middleware($mware);
            }
        }

        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }

        $this->params = $route_params;
    }

    public function middleware(string $middleware)
    {
        $middleware = Str::studly($middleware) . 'Middleware';
        $middleware = 'App\Framework\Middleware\\' . $middleware;
        $middleware = new $middleware;
        return $middleware::middleware();
    }

    public function __call($name, $args)
    {
        $method = $name . 'Action';
        if (method_exists($this, $method)) {
            $this->before();
            call_user_func_array([$this, $method], $args);
            $this->after();
        } else {
            throw new \Exception("Method $method not found in controller" . get_class($this));
        }
    }

//    public static function __callStatic()
//    {
//
//    }

//    /**
//     * The route parameters bag from the URL.
//     * @var array
//     */
//    protected static $route_params = [];
//
//    /**
//     * Set the route parameters.
//     * @param array $route_params
//     * @return void
//     */
//    public static function setRouteParams(array $route_params): void
//    {
//        static::$route_params = $route_params;
//    }
//
//    /**
//     * Get the current route parameters.
//     * @return array
//     */
//    public static function getRouteParams(): array
//    {
//        return static::$route_params;
//    }
}