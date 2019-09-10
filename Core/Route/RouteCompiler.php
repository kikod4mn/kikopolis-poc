<?php

namespace Kikopolis\Core\Route;

class RouteCompiler
{
    private $route_array = [
        'home' => \App\Controllers\Http\Home::class,
        'more' => \App\Controllers\More::class,
        'posts' => \App\Controllers\Posts::class,
        'show' => \App\Controllers\Show::class
    ];

    /**
     * @var string
     */
    private $pattern = '';
    /**
     * @var string
     */
    private $controller = '';
    /**
     * @var string
     */
    private $method = '';

    /**
     * RouteCompiler constructor
     *
     * @param string $pattern
     * @param string $controller
     * @param string $method
     */
    public function __construct(string $pattern = '', string $controller = '', string $method = '')
    {
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->method = $method;
    }

    public function getRoutes()
    {
        return $this->route_array;
    }
}