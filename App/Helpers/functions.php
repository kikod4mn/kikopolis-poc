<?php

use Kikopolis\App\Config\Config;

if (!function_exists('redirect')) {
    function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}