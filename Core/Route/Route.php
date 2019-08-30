<?php

namespace Kikopolis\Core\Route;

class Route
{
    /**
     * The route HTTP method.
     *
     * @var string
     */
    private $method = '';

    /**
     * The route to match.
     *
     * @var string
     */
    private $route = '';

    /**
     * The params as array. Controller at 0 and Method at 1.
     *
     * @var array
     */
    private $params = [];

    /**
     * The route options.
     *
     * @var array
     */
    private $options = [];

    /**
     * The namespace of the controller.
     *
     * @var string
     */
    private $namespace = '';

    /**
     * The name of the route.
     *
     * @var string
     */
    private $route_name = '';

    /**
     * Route constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        return $this->handle($options) ? $this : 'Error occurred. Array improperly set.';
    }

    /**
     * Handle the array of options for the route
     *
     * @param array $options
     * @throws Exception
     * @return $this
     */
    public function handle(array $options)
    {
        if (!isset($options['method'])) {
            throw new \Exception('Pattern is required');
        }
        if (!isset($options['controller'])) {
            throw new \Exception('Controller is required');
        }
        if (!isset($options['method'])) {
            throw new \Exception('Method is required');
        }
        $this->method = $options['method'];
        $this->controller = $options['route'];
        $this->params = $options['params'];
        $this->options = $options['options'];
        $this->namespace = $options['namespace'];
        $this->route_name = $options['route_name'];
        return $this;
    }
}