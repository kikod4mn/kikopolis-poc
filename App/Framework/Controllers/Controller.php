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
    /**
     * The array for specifying the controllers middleware.
     * @var array
     */
    protected $middleware = [];

    /**
     * Parameter bag from the GET array
     * @var array
     */
    protected $params = [];

    /**
     * Run before a called method-
     */
    protected function before() {
        //
    }

    /**
     * Run after a called method.
     */
    protected function after() {
        //
    }

    /**
     * Controller constructor.
     * Since middleware and params are set in this, the controller is final and should not be overridden.
     * @param array $route_params
     */
    final public function __construct($route_params = [])
    {
        if ($this->middleware !== []) {
            foreach ($this->middleware as $mware) {
                if ($this->middleware($mware) === false) {
                    throw new \Exception(sprintf("Middleware [%s] is preventing controller [%s] from running.", $mware, get_class($this)), 404);
                }
            }
        }

        if (method_exists(get_called_class(), '__constructor')) {
            $this->__constructor();
        }

        $this->params = $route_params;
    }

    /**
     * Run the middleware init method, aptly called middleware that is used as the main method to bootstrap the middleware.
     * @param string $middleware
     * @return mixed
     */
    public function middleware(string $middleware)
    {
        $middleware = Str::studly($middleware) . 'Middleware';
        $middleware = 'App\Framework\Middleware\\' . $middleware;
        $middleware = new $middleware;
        return $middleware::middleware();
    }

    /**
     * Handle calls into the controller.
     * All methods that are routes are expected to have a prefix of Action.
     * @param $name
     * @param $args
     * @throws \Exception
     */
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
}