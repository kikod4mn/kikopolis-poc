<?php

declare(strict_types=1);

namespace Kikopolis\Core\Route;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Cookie
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class RouteCompiler
{
    /**
     * Array of controllers for uri comparison.
     * @var array
     */
    private $bindings = [
        'home' => App\Http\Controllers\Home::class,
        'posts' => App\Http\Controllers\Posts::class,
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
     * RouteCompiler construct
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
        return $this->bindings;
    }
}