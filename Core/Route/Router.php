<?php

namespace Kikopolis\Core\Route;

defined('_KIKOPOLIS') or die('No direct script access!');

use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Helpers\Str;
use Kikopolis\Core\Container;

class Router
{
    /**
     * The controllers namespace.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Array of all routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Array for the current route being processed.
     *
     * @var array
     */
    protected $route = [];

    /**
     * The current controller.
     *
     * @var string
     */
    protected $controller = '';

    /**
     * The current method.
     *
     * @var string
     */
    protected $method = '';

    /**
     * Parameters of the route.
     * Controller at 0 and method at 1.
     *
     * @var array
     */
    protected $action = [];

    /**
     * The current route options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The DI container.
     *
     * @var \Kikopolis\Core\Container
     */
    protected $container = '';

    /**
     * Add a route to the routing table.
     *
     * @param array|string  $method
     * @param string        $uri
     * @param string        $action
     * @param array         $options
     * @param string        $namespace
     * @return void
     */
    public function add($method, string $uri, string $action, array $options = [])
    {
        // This variable exists to use the uri incase the url is dynamic and does not contain some parts.
        // This assumes that $action has been left as an empty string as by default.
        // If the controller is there but the method isn't then we will parse it differently and instead use the
        // $uri_old to figure out which controller and method to use.
        // This uses the same logic as the dot syntax $action.
        // 0 for Controller and 1 for the Method in the final action array key.
        $uri_old = $uri;
        // Convert the route to a regular expression: escape forward slashes
        $uri = preg_replace('/\//', '\\/', $uri);
        // Convert variables e.g. {controller}
        $uri = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $uri);
        // Convert variables with custom regular expressions e.g. {id:\d+}
        $uri = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $uri);
        // Add start and end delimiters, and case insensitive flag
        $uri = '/^' . $uri . '$/i';
        // Add the route to the routes array
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $this->parseAction($action, $uri_old),
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

    public function head($uri, $action, $options = [])
    {
        $this->add('HEAD', $uri, $action, $options);
        return $this;
    }

    public function get($uri, $action, $options = [])
    {
        $this->add('GET', $uri, $action, $options);
        return $this;
    }

    public function post($uri, $action, $options = [])
    {
        $this->add('POST', $uri, $action, $options);
        return $this;
    }

    public function put($uri, $action, $options = [])
    {
        $this->add('PUT', $uri, $action, $options);
        return $this;
    }

    public function patch($uri, $action, $options = [])
    {
        $this->add('PATCH', $uri, $action, $options);
        return $this;
    }

    public function delete($uri, $action, $options = [])
    {
        $this->add('DELETE', $uri, $action, $options);
        return $this;
    }

    /**
     * Dispatch the current route
     *
     * @param string $uri
     * @return void
     */
    public function dispatch(string $uri = '/')
    {
        if ($uri === '') {
            $uri = '/';
        }
        // Instantiate the container
        $this->container = new Container();
        // Check if the $uri isset and remove query string variables
        $url = isset($uri) ? $this->removeQueryStringVariables(Str::u($uri)) : '/';
        // Match the url
        $this->match($url);
        // Run the base controller to set the route parameters eg ID, slug etc
        Controller::setRouteParams($this->route['params']);
        // Send the controller and method to the Container
        // The container will instantiate the correct method with dependencies
        $this->container->get($this->controller, $this->method, [], $this->route['params']);
    }

    /**
     * Parse the action parameter of the url to a usable array format.
     * If no action is specified, uses the uri part of the url to try to find a suitable controller and method.
     *
     * @param string $action
     * @param string $uri
     * @return array
     */
    protected function parseAction(string $action, string $uri = '')
    {
        return Str::parseDotSyntax($action);
        // if ($uri === '') {
        //     return Str::parseDotSyntax($action);
        // }
        // return Str::parseSlashSyntax($uri);
    }

    /**
     * Match the url to a route
     *
     * @param string $url
     * @return $this
     */
    protected function match($url)
    {
        // Check if the route is in our array
        foreach ($this->routes as $route) {
            if (preg_match($route['uri'], $url, $matches)) {
                // If the route is found, set it as the route and break from the loop
                $this->route = $route;
                // Set the route namespace
                if (array_key_exists('namespace', $this->route['options'])) {
                    $this->route['options']['namespace'] = $this->setNamespace($this->route['options']['namespace']);
                } else {
                    $this->route['options']['namespace'] = $this->setNamespace();
                }
                break;
            }
        }
        // if no match is found then throw an exception
        if (empty($this->route)) {
            throw new \Exception('No route is matched.', 404);
        }
        // Verify that the request method part is set in the route
        if (!isset($this->route['method'])) {
            throw new \Exception('Route REQUEST_METHOD cannot be blank. Please check your routing table for the route' . $this->route['route']);
        }
        // Check if server query method and allowed method for the url match
        if (!$this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->route['method'])) {
            throw new \Exception('Request method does not match the allowed method for the route.');
        }
        // Assign the controller and unset its array value
        if (!$this->controller = $this->route['options']['namespace'] . Str::convertToStudlyCase($this->route['action'][0])) {
            throw new \Exception('Error setting controller property.');
        }
        unset($this->route['action'][0]);
        // Assign the method and unset its array value
        if (!$this->method = Str::convertToCamelCase($this->route['action'][1])) {
            throw new \Exception('Error setting method property.');
        }
        unset($this->route['action'][1]);
        // Get extra parameters from the url if they exist
        $this->route['params'] = $this->extractRouteParameters($url);
        return $this;
    }

    /**
     * Extract the route parameters from the url
     *
     * @param string $url
     * @return array
     */
    protected function extractRouteParameters(string $url)
    {
        $params = [];
        if (preg_match($this->route['uri'], $url, $matches)) {
            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $params[$key] = $match;
                }
            }
        }
        return $params;
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
     * Set the correct namespace for the route.
     * Namespace is defined in the route parameters in the routing table.
     * To instantiate the controller class we need the correct namespace.
     * Default namespace is App\Http\Controllers\{controller}.
     * Where {controller} is the name of the class.
     * 
     * @return void
     */
    protected function setNamespace($namespace = null)
    {
        $namespace = !is_null($namespace) && is_string($namespace) ? 'App\Http\Controllers\\' . $namespace . '\\' : 'App\Http\Controllers\\';
        return $namespace;
    }
}