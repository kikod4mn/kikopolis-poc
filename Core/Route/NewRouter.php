<?php

declare(strict_types=1);

namespace Kikopolis\Core\Route;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * NewRouter
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class NewRouter
{
    private $routes = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }
}