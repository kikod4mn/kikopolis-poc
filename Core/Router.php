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
     * The parameters of the route.
     * Controller at 0 and Method at 1.
     *
     * @var array
     */
    protected $params;

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
    // protected function getUrlAsArray($url)
    // {
    //     $url = rtrim($url, '/');
    //     $url = filter_var($url, FILTER_SANITIZE_URL);
    //     $url = explode('/', $url);
    //     return $url;
    // }

    public function dispatch($url)
    {
        // Remove query string variables
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            // if route matches
            $controller_object = new $this->currentController();
            // var_dump($controller_object);
            if (method_exists($controller_object, $this->currentMethod)) {
                // echo "method exists";
                $method = $this->currentMethod;
                $controller_object->$method();
            }
        } else {
            // if route does not match
        }
    }

    public function match($url)
    {
        // Check if the route is in our array
        if (!array_key_exists($url, $this->routes)) {
            echo "No route found";
            die();
        }
        // var_dump($this->routes[$url]);
        $this->route = $this->routes[$url];
        // var_dump($this->route);

        // Check the server query method against allowed route method
        // var_dump($this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->routes[$url]['method']));
        if (!$this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->routes[$url]['method'])) {
            echo "REQUEST_METHOD does not match";
            die();
        }
        // Get the url as an array. Divide the parts up to check if the Controller file and Methods exist
        // $parts = $this->getUrlAsArray($url);
        // Validate controller
        // $controller = $this->convertToStudlyCase($parts[0]);
        // Set controller and method from the params of the url in the routing table
        $this->getParamsAsArray();
        // Convert the name to StudlyCase
        $this->currentController = $this->convertToStudlyCase($this->params[0]);
        // Convert the name to camelCase
        $this->currentMethod = $this->convertToCamelCase($this->params[1]);
        // Validate the controller file exists and if it does, will require the file
        if (!$this->validateController($this->currentController)) {
            echo "No controller found";
            die();
        } else {
            // Set the correct full namespace for the controller
            $this->setNamespace();
            // var_dump($this);
            // Instantiate the controller class
            $this->currentController = $this->route['namespace'] . $this->currentController;
        }
        // Set the correct full namespace for the controller
        // $this->setNamespace();
        // var_dump($this->route['namespace'] . $this->currentController);
        // $this->currentController = $this->route['namespace'] . $this->currentController;
        // if (class_exists($this->currentController)) {
        //     echo "class exists";
        // }

        // Require the controller file
        // require_once Config::getAppRoot() . '/App/Controllers/' . $controller . '.php';

        // var_dump($this->route);
        // var_dump($controller);

        // Validate method
        // $method = $this->convertToCamelCase($parts[1]);
        // $this->validateMethod(lcfirst($url[1]));

        // var_dump($this->route);
        // var_dump($url);
        // var_dump($parts);
        // var_dump($_SERVER["REQUEST_METHOD"]);
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
     * Check if the controller file exists and require it.
     *
     * @throws Exception
     * 
     * @param string $controller
     * 
     * @return object
     */
    protected function validateController($controller)
    {
        if (file_exists(Config::getAppRoot() . '/App/Controllers/' . $controller . '.php')) {
            // var_dump(Config::getAppRoot() . '/App/Controllers/' . $controller . '.php');
            require_once Config::getAppRoot() . '/App/Controllers/' . $controller . '.php';
            return true;
        } else {
            echo "Controller file not readable or does not exist";
            die();
        }
    }

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