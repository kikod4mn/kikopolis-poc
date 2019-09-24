<?php

use Kikopolis\App\Config\Config;

if (!function_exists('redirect')) {
    function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}

// Escape the content of a variable for output to HTML.
if (!function_exists('escape')) {
    function escape($var)
    {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}

// Allow a list of html tags to pass through the escaping.
if (!function_exists('outputSafeHtml')) {
    function outputSafeHtml($var)
    {
        $var = htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        return htmlspecialchars_decode($var);
    }
}

// Output misc value to HTML. Array values, object properties etc.
if (!function_exists('outputMiscValue')) {
    function outputMiscValue($stack, $key)
    {
        switch ($stack) {
            case is_array($stack);
                return $stack[$key];
            case is_object($stack);
                $stack = get_object_vars($stack);
                return $stack[$key];
                // return print_r($stack->$key, true);
                // return $stack->{"$key"};
            case class_exists($stack):
                // @TODO: Concept for using class public methods aswell to try and get a property.
                return $stack->get . $key;
            default:
                return null;
        }
        return null;
    }
}
