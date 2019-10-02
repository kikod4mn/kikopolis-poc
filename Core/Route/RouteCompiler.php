<?php

declare(strict_types=1);

namespace Kikopolis\Core\Route;

class RouteCompiler
{
    private $bindings_array = [
        'home' => \App\Controllers\Http\Home::class,
        'posts' => \App\Controllers\Posts::class,
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

    public function getBindings()
    {
        return $this->bindings_array;
    }
}