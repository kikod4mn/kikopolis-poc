<?php

namespace Kikopolis\Core;

defined('_KIKOPOLIS') or die('No direct script access!');

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
     * Temporary variable to hold the route being processed.
     *
     * @var array
     */
    protected $route;

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
    public function add(string $method, string $route, string $params, array $options = [], string $namespace = '')
    {
        $this->routes[$route] = [
            'method' => $method,
            'route' => $route,
            'params' => $params,
            'options' => $options,
            'namespace' => $namespace
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

    /**
     * Divide the server query string up by /.
     *
     * @return array
     */
    protected function getUrlAsArray($url)
    {
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
    }

    public function dispatch($url)
    {
        // Remove query string variables
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) { }
    }

    public function match($url)
    {
        // Check if the route is in our array
        if (!array_key_exists($url, $this->routes)) {
            echo "No route found";
        }
        // var_dump($this->routes[$url]);
        $this->route = $this->routes[$url];
        // var_dump($this->route);

        // Check the server method against allowed route method
        // var_dump($this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->routes[$url]['method']));
        $this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->routes[$url]['method']);
        // Get the url as an array. Divide the parts up to check if the Controller file and Methods exist
        $parts = $this->getUrlAsArray($url);
        // Validate controller
        $controller = $this->convertToStudlyCase($parts[0]);
        if (!$this->validateController($controller)) {
            echo "No controller found";
        }
        // Set the correct full namespace for the controller
        $this->setNamespace();
        var_dump($this->route['namespace'] . $controller);
        $controller = $this->route['namespace'] . $controller;
        if (class_exists($controller)) {
            $controller_object = new $controller();
            var_dump($controller_object);
        }

        // Require the controller file
        // require_once Config::getAppRoot() . '/App/Controllers/' . $controller . '.php';

        // var_dump($this->route);
        // var_dump($controller);

        // Validate method
        $method = $this->convertToCamelCase($parts[1]);
        // $this->validateMethod(lcfirst($url[1]));

        var_dump($this->route);
        // var_dump($url);
        // var_dump($parts);
        // var_dump($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * Remove the query string variables from the URL.
     * The route takes in the full query string and allowing variables will produce errors
     * 
     * localhost/index?page=1 => localhost/index
     * 
     * A format of localhost/?page will not work
     *(NB. The .htaccess file converts the first ? to a & when it's passed through to the $_SERVER variable).
     * 
     * @param string $url The full url
     * 
     * @return string $url URL with the variables removed
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    /**
     * Match the server query method to the accepted method for the route.
     *
     * @param string $method
     * 
     * @return boolean
     */
    protected function methodMatchCheck(string $request_method, $url_method)
    {
        return $request_method === $url_method;
    }

    /**
     * Check if the controller file exists.
     *
     * @throws Exception
     * 
     * @param string $controller
     * 
     * @return object
     */
    protected function validateController($controller)
    {
        return file_exists(Config::getAppRoot() . '/App/Controllers/' . $controller . '.php');
    }

    protected function validateMethod($method)
    {
        // Instantiate controller class
        var_dump($this->currentController);
        $this->currentController = new $this->currentController;

        echo $method;
        var_dump(method_exists($this->currentController, $method));
        return method_exists($this->currentController, $method);
    }

    /**
     * Convert the string with dashes to StudlyCase
     * product-category to ProductCategory
     * 
     * @param string the string to convert
     * 
     * @return string
     */
    protected function convertToStudlyCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string to camelCase
     * product-category to productCategory
     * 
     * @param string the string to convert
     * 
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCase($string));
    }

    /**
     * Set the correct namespace for the route.
     * Namespace is defined in the route parameters in the routing table.
     * To instantiate the controller class we need the correct namespace.
     * Default namespace is Kikopolis\App\Controllers\{controller}.
     * Where {controller} is the name of the class.
     * However, should there be a need to divide controllers into folders and thus consequently different namespaces,
     * you can pass that namespace in as the last argument to the Route in the add() method.
     * 
     * @return string The request URL
     */
    protected function setNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->route)) {
            // If there is no namespace passed in then default namespace is used.
            // Check for an empty array value and set to default namespace if.
            if (empty($this->rount['namespace'])) {
                $this->route['namespace'] = $namespace;
            } else {
                $this->route['namespace'] = $namespace . $this->route['namespace'] . '\\';
                // var_dump($this->route);
            }
        }

        // return $namespace;
    }
}