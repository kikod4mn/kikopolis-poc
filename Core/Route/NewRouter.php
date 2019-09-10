<?php

namespace Kikopolis\Core\Route;

class NewRouter
{
    private $routes = [];

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }
}