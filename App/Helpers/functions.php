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
        $var = '';
        switch ($stack) {
            case is_array($stack);
                $var = $stack[$key];
                break;
            case is_object($stack);
                $var = $stack->{"$key"};
                break;
            case class_exists($stack):
                // @TODO: Concept for using class public methods aswell to try and get a property.
                $var = $stack->get . $key;
                break;
            default:
                $var = null;
        }
        return $var;
    }
}
