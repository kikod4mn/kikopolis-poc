<?php

declare(strict_types=1);

namespace Kikopolis\Core\Aurora;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Aurora function helper. Add custom functions with these methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class AuroraFunctionHelper
{
    /**
     * @var array
     */
    private static $functions = [];

    /**
     * Return all the functions.
     * @return array
     */
    public static function getFunctions(): array
    {
        return static::$functions;
    }

    /**
     * Add a custom function to the array.
     * @param $name
     * @param $callback
     * @param array $arguments
     * @return void
     */
    public static function addFunction(string $name, \Closure $callback, array $arguments = []): void
    {
        static::$functions[$name] = ['name' => $name, 'closure' => $callback, 'arguments' => $arguments];
    }
}
