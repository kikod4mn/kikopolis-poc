<?php

use Kikopolis\App\Config\Config;

if (!function_exists('redirect')) {
    function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}

if (!function_exists('escape')) {
    function escape($var, $escape = 'escape', $key = '')
    {
        // First we determine if the $var passed in is not a string
        // and pass it back to this function recursively with the $key for echoing to template.
        $var = escape_type($var, $key);
        // Different escape levels, depending on the surrounding tags of the $var.
        switch ($escape) {
            case 'escape':
                return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
            case 'allow-html':
                return htmlspecialchars_decode(htmlspecialchars($var, ENT_QUOTES, 'UTF-8'));
            case 'no-escape':
                return $var;
            default:
                return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        }
    }
}

if (!function_exists('escape_type')) {
    function escape_type($var, $key)
    {
        switch ($var) {
            case is_iterable($var) && is_array($var):
                return (string) $var[$key];
            case is_object($var) || ($var instanceof \Traversable):
                $var = get_object_vars($var);
                return (string) $var[$key];
                // return print_r($stack->$key, true);
                // return $stack->{"$key"};
            case is_int($var):
                return (string) $var;
            case is_string($var):
                return (string) $var;
        }
    }
}
