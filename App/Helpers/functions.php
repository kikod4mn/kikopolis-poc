<?php

use Kikopolis\App\Config\Config;

if (!function_exists('redirect')) {
    function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}

if (!function_exists('k_echo')) {
    function k_echo($var, $escape = 'escape', $key = '')
    {
        // First we determine if the $var passed in is not a string
        // and pass it back to this function recursively with the $key for echoing to template.
        switch ($var) {
            case is_iterable($var) && is_array($var):
                return k_echo((string) $var[$key], $escape);
            case is_object($var) || ($var instanceof \Traversable):
                $var = get_object_vars($var);
                return k_echo((string) $var[$key], $escape);
                // return print_r($stack->$key, true);
                // return $stack->{"$key"};
        }
        // Different escape levels, depending on the surrounding tags of the $var.
        switch ($escape) {
            case 'escape':
                return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
            case 'allow-html':
                return htmlspecialchars_decode(htmlspecialchars($var, ENT_QUOTES, 'UTF-8'));
            case 'no-escape':
                return $var;
            default:
                // var_dump($var);
                return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        }
    }
}
