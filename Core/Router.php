<?php

namespace Kikopolis\Core;

use Kikopolis\App\Config\Config;

class Router
{
    /**
     * The routing table
     *
     * @var array
     */
    protected $routes = [];

    /**
     * The controller to route to.
     *
     * @var string
     */
    protected $currentController;

    /**
     * The method in the controller
     *
     * @var string
     */
    protected $currentMethod;

    /**
     * Add aroute to the routing table.
     *
     * @param string $method
     * @param string $route
     * @param string $params
     * @param array $options
     *
     * @return void
     */
    public function add(string $method, string $route, string $params, array $options = [])
    {
        $this->routes[$route] = [
            'method' => $method,
            'route' => $route,
            'params' => $params,
            'options' => $options
        ];
    }

    /**
     * Show the current routing table.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function dispatch()
    {
        // Check for the method match.
        $url = $this->getUrl();

        var_dump($url);

        var_dump($this->validateController(ucwords($url[0])));

        // $this->methodMatchCheck($method);


        // $this->route = explode('.', $params);
        // $this->currentController = $this->route[0];
        // $this->currentMethod = $this->route[1];
    }

    protected function methodMatchCheck(string $method)
    {
        return $this->routes['method'] === $method;
    }

    protected function getUrl()
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            $url = rtrim($_SERVER['QUERY_STRING'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }

    protected function validateController($controller)
    {
        var_dump(Config::getAppRoot() . '/App/Controllers/' . $controller . '.php');
        return file_exists(Config::getAppRoot() . '/App/Controllers/' . $controller . '.php');
    }
}