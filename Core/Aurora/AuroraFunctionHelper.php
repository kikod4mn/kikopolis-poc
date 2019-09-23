<?php

namespace Kikopolis\Core\Aurora;

use ReflectionFunction;

defined('_KIKOPOLIS') or die('No direct script access!');

class AuroraFunctionHelper
{
    private static $functions = [];

    public function __construct()
    {
        //
    }

    public static function getFunctions()
    {
        return static::$functions;
    }

    public static function addFunction($name, $callback, $arguments = [])
    {
        static::$functions[$name] = ['closure' => $callback, 'arguments' => $arguments];
    }
}
