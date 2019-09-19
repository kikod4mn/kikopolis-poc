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
