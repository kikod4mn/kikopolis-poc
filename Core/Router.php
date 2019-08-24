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
    protected $route = [];

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
     * The parameters of the route.
     * Controller at 0 and Method at 1.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Protected options array for the route.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Add a route to the routing table.
     *
     * @param string|array $method
     * @param string $route
     * @param string $params
     * @param array $options
     *
     * @return void
     */
    public function add($method, string $route, string $params, array $options = [], string $namespace = '')
    {
        // Assign the name of the route
        $route_name = explode('.', $params);
        $route_name = ucwords($route_name[0]) . '-' . ucwords($route_name[1]);
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route_name] = [
            'method' => $method,
            'route' => $route,
            'params' => $params,
            'options' => $options,
            'namespace' => $namespace,
            'route_name' => $route_name
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
     * Check if controller and method exist, require the controller file and route to the method.
     *
     * @throws \Exception
     * 
     * @param string $url
     * 
     * @return void
     */
    public function dispatch($uri)
    {
        // Check if the $uri isset and remove query string variables
        $url = isset($uri) ? $this->removeQueryStringVariables(filter_var($uri, FILTER_SANITIZE_URL)) : '/';
        // Match the url
        if ($this->match($url)) {
            // Check if controller file exists and require it
            if (!empty($this->route['namespace'])) {
                $namespace = $this->route['namespace'] . '/';
            }
            if (file_exists(Config::getAppRoot() . '/App/Controllers/' . $namespace . $this->currentController . '.php')) {
                require_once Config::getAppRoot() . '/App/Controllers/' . $namespace . $this->currentController . '.php';
            } else {
                throw new \Exception("Controller - " . $this->route['route_name'] . " - does not exist or file is not readable");
            }
            // Set the correct full namespace for the controller
            $this->setNamespace();
            // Set the namespace to instantiate the controller
            $this->currentController = $this->route['namespace'] . $this->currentController;
            // Instantiate the controller class
            $controller_object = new $this->currentController();
            // Check if the method exists and route to the method
            if (method_exists($controller_object, $this->currentMethod)) {
                $method = $this->currentMethod;
                // If there are more parameters to the url, use them here
                if (!empty($this->params)) {
                    $this->options = array_splice($this->params, 0, 0);
                    foreach ($this->params as $key => $value) {
                        echo $key . ' - ' . $value . '<br>';
                    }
                }
                // Check what?
                if (preg_match('/method$/i', $method) == 0) {
                    $controller_object->$method();
                } else {
                    throw new \Exception("Method - '$this->currentMethod' - in controller - '$this->currentController' - cannot be called directly - remove the Action suffix to call this method");
                }
            } else {
                throw new \Exception("Method - '$this->currentMethod' - not found in class - '$this->currentController'");
            }
        } else {
            throw new \Exception('No route matched', 404);
        }
    }

    /**
     * Match the current url to the routing table.
     * 
     * @throws \Exception
     *
     * @param string $url
     * 
     * @return boolean
     */
    public function match($url)
    {
        // Check if the route is in our array
        foreach ($this->routes as $route) {
            if (preg_match($route['route'], $url, $matches)) {
                // echo $route['route'];
                $this->route = $route;
                // var_dump($this->route);
            }
        }
        // Check the server query method against allowed route method
        if (!$this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->route['method'])) {
            throw new \Exception('Request method does not match the allowed method for the route.');
        }
        // Set controller and method from the params of the url in the routing table
        $this->getParamsAsArray();
        // Convert the name to StudlyCase and unset the value
        $this->currentController = $this->convertToStudlyCase($this->params[0]);
        unset($this->params[0]);
        // Convert the name to camelCase and unset the value
        $this->currentMethod = $this->convertToCamelCase($this->params[1]);
        unset($this->params[1]);
        // Return
        return true;
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
     * Checks if the methods allowed for the route are in an array, meaning multiple methods would be allowed.
     *
     * @param string $request_method    $_SERVER['REQUEST_METHOD'].
     * @param string|array $url_method  The method from the route in the routing table.
     * 
     * @return boolean
     */
    protected function methodMatchCheck(string $request_method, $url_method)
    {
        if (is_array($url_method)) {
            foreach ($url_method as $method) {
                if ($request_method === $method) {
                    return true;
                } else {
                    continue;
                }
            }
        } else {
            return $request_method === $url_method;
        }
    }

    /**
     * Divide the parameters to an array.
     * Controller at index 0.
     * Method at index 1.
     * Rest of the url from index 2 and onwards if exist.
     *
     * @return array
     */
    protected function getParamsAsArray()
    {
        $this->params = explode('.', $this->route['params']);
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
     * @return void
     */
    protected function setNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->route)) {
            // If there is no namespace passed in then default namespace is used.
            // Check for an empty array value and set to default namespace if is empty.
            if (empty($this->route['namespace'])) {
                $this->route['namespace'] = $namespace;
            } else {
                $this->route['namespace'] = $namespace . $this->route['namespace'] . '\\';
            }
        }
    }
}