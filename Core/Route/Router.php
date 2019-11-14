<?php

declare(strict_types=1);

namespace Kikopolis\Core\Route;

use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Helpers\Str;
use Kikopolis\Core\Container\Container;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Cookie
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

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
     * Action parameters of the route.
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
     * The IoC container.
     *
     * @var \Kikopolis\Core\Container\Container
     */
    protected $container = null;

    /**
     * Add a route to the routing table.
     *
     * @param array|string  $method
     * @param string        $uri
     * @param string        $action
     * @param array         $options
     * @return void
     */
    public function add($method, string $uri, string $action, array $options = []): void
    {
        // Add the route to the routes array
        $this->routes[] = [
            'method' => $method,
            'uri' => $this->parseUrl($uri),
            'action' => $this->parseAction($action),
            'options' => $options
        ];
    }

    /**
     * Show the current routing table.
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Add a route to the routing table with the HEAD method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function head($uri, $action, $options = [])
    {
        $this->add('HEAD', $uri, $action, $options);
        return $this;
    }

    /**
     * Add a route to the routing table with the GET method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function get($uri, $action, $options = [])
    {
        $this->add('GET', $uri, $action, $options);
        return $this;
    }

    /**
     * Add a route to the routing table with the POST method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function post($uri, $action, $options = [])
    {
        $this->add('POST', $uri, $action, $options);
        return $this;
    }

    /**
     * Add a route to the routing table with the PUT method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function put($uri, $action, $options = [])
    {
        $this->add('PUT', $uri, $action, $options);
        return $this;
    }

    /**
     * Add a route to the routing table with the PATCH method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function patch($uri, $action, $options = [])
    {
        $this->add('PATCH', $uri, $action, $options);
        return $this;
    }

    /**
     * Add a route to the routing table with the DELETE method.
     *
     * @param string $uri
     * @param string $action
     * @param array $options
     * @return Router
     */
    public function delete($uri, $action, $options = [])
    {
        $this->add('DELETE', $uri, $action, $options);
        return $this;
    }

    public function resource($uri, $controller, $options = [], $except = [], $only = [])
    {
        $routes = [
            'index' => ['GET', $uri . '/index', $controller . '.index' , $options],
            'show' => ['GET', $uri . '/{id}/show', $controller . '.show' , $options],
            'edit' => ['GET', $uri . '/{id}/edit', $controller . '.edit' , $options],
            'update' => ['POST', $uri . '/{id}/update', $controller . '.update' , $options],
            'create' => ['GET', $uri . '/create', $controller . '.create' , $options],
            'insert' => ['POST', $uri . '/insert', $controller . '.insert' , $options],
            'delete' => ['DELETE', $uri . '/{id}/delete', $controller . '.delete' , $options],
        ];
		if ($except !== []) {
			foreach ($except as $value) {
				unset($routes[$value]);
			}
		}
		if ($only !== [] && $except === []) {
			foreach ($only as $value) {
				$this->add($routes[$value]['0'], $routes[$value]['1'], $routes[$value]['2'], $routes[$value]['3']);
			}
		} else {
			foreach ($routes as $route) {
				$this->add($route['0'], $route['1'], $route['2'], $route['3']);
			}
		}
    }

    /**
     * Dispatch the current route
     *
     * @param string $uri
     * @return void
     * @throws \Exception
     */
    public function dispatch(string $uri = ''): void
    {
        // If the $uri is an empty string, set it to the default that routes to the homepage.
        if ($uri === '') {
            $uri = '/';
        } else {
            $uri = $this->removeTrailingSlash($uri);
        }
        // Instantiate the container
        $this->container = new Container();
        // Check if the $uri isset and remove query string variables
        $url = isset($uri) ? $this->removeQueryStringVariables(Str::u($uri)) : '/';
        // Match the url
        $this->match($url);
        // Set the return page if it is using a get method
        if ($this->methodMatchCheck($this->route['method'], 'GET')) {
            $_SESSION['previous_page'] = $uri;
        }
        // Run the base controller to set the route parameters eg ID, slug etc
//        Controller::setRouteParams($this->route['params']);
        // Send the controller and method to the Container
        // The container will instantiate the correct method with dependencies
        $args = $this->container->get($this->controller, $this->method, []);
//        var_dump($this->controller);
//        var_dump($this->method);
        $controller = new $this->controller($this->route['params']);
        // If the method name ends in Action or action, only then will said method be called.
        if (preg_match('/action$/i', $this->method) === 0) {
            $controller->{$this->method}(...$args);
        }
    }

    /**
     * Remove slashes from the front and back of the string.
     *
     * @param string $uri
     * @return string
     */
    protected function removeTrailingSlash(string $uri): string
    {
        return ltrim(rtrim($uri, '/'), '/');
    }

    /**
     * Parse the url to a Regex and if applicable, convert variables to capture groups.
     *
     * @param string $url
     * @return string
     */
    protected function parseUrl(string $url): string
    {
        // Remove trailing slash if the $url is not simply a slash indicating the home route
        if ($url !== '/') {
            $url = $this->removeTrailingSlash($url);
        }
        // Initialize variables
        $params = [];
        // Convert the route to a regular expression: escape forward slashes
        $url = preg_replace('/\//', '\\/', $url);
        // Convert variables e.g. {id}, {slug} to capture groups
        $url = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-zA-Z0-9-_]+)', $url);
        // Add start and end delimiters, and case insensitive flag
        $url = '/^' . $url . '$/i';
        return $url;
    }

    /**
     * Parse the action parameter of the url to a usable array format.
     * If no action is specified, uses the uri part of the url to try to find a suitable controller and method.
     *
     * @param string $action
     * @return array
     */
    protected function parseAction(string $action): array
    {
        return Str::dot($action);
    }

    /**
     * Match the url to a route
     *
     * @param string $url
     * @return Router
     * @throws \Exception
     */
    protected function match(string $url)
    {
        // Check if the route is in our array.
        foreach ($this->routes as $route) {
            if (preg_match($route['uri'], $url)) {
                // If the route is found, set it as the route and break from the loop.
                $this->route = $route;
                // Set the route namespace.
                // If the namespace exists, we will add the defined namespace through the setNameSpace function to the default one.
                // If no namespace is found, the default will be used.
                if (array_key_exists('namespace', $this->route['options'])) {
                    $this->route['options']['namespace'] = $this->setNamespace($this->route['options']['namespace']);
                } else {
                    $this->route['options']['namespace'] = $this->setNamespace();
                }
                break;
            }
        }
        // If no matching route is found then throw an exception.
        if (empty($this->route)) {
            throw new \Exception('No route is matched.', 404);
        }
        // Verify that the request method part is set in the route.
        if (!isset($this->route['method'])) {
            throw new \Exception('Route REQUEST_METHOD cannot be blank. Please check your routing table for the route' . $this->route['route']);
        }
        // Check if server query method and allowed method for the url match.
        if (!$this->methodMatchCheck($_SERVER['REQUEST_METHOD'], $this->route['method'])) {
            throw new \Exception('Request method does not match the allowed method for the route.');
        }
        // Assign the controller and unset its array value.
        if (!$this->controller = $this->route['options']['namespace'] . Str::studly($this->route['action'][0])) {
            throw new \Exception("Error setting controller property - {$this->route['action'][0]}");
        }
        unset($this->route['action'][0]);
        // Assign the method and unset its array value.
        if (!$this->method = Str::camel($this->route['action'][1])) {
            throw new \Exception("Error setting method property - {$this->route['action'][0]}");
        }
        unset($this->route['action'][1]);
        // Get extra parameters from the url if they exist.
        $this->route['params'] = $this->extractRouteParameters($url);

        return $this;
    }

    /**
     * Extract the route parameters from the url
     *
     * @param string $url
     * @return array
     */
    protected function extractRouteParameters(string $url): array
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
     * @return string $url URL with the variables removed
     */
    protected function removeQueryStringVariables(string $url): string
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
     * @return bool
     */
    protected function methodMatchCheck(string $request_method, $url_method): bool
    {
        switch ($url_method) {
            case is_string($url_method):
                return strtolower($request_method) === strtolower($url_method);
            case is_array($url_method):
                foreach ($url_method as $method) {
                    if (strtolower($request_method) === strtolower($method)) {
                        return true;
                    } else {
                        continue;
                    }
                }
        }
        return false;
    }

    /**
     * Set the correct namespace for the route.
     * Namespace is defined in the route parameters in the routing table.
     * To instantiate the controller class we need the correct namespace.
     * Default namespace is App\Http\Controllers\{controller}.
     * Where {controller} is the name of the class.
     * 
     * @param string $namespace
     * @return string
     */
    protected function setNamespace(string $namespace = ''): string
    {
        $namespace = $namespace !== '' ? 'App\Http\Controllers\\' . $namespace . '\\' : 'App\Http\Controllers\\';
        return $namespace;
    }
}
