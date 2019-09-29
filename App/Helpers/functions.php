<?php

use Kikopolis\App\Config\Config;
use Kikopolis\App\Utility\Token;

if (!function_exists('redirect')) {
    function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        $token = new Token();
        return $token->getCsrfToken();
    }
}

if (!function_exists('csrf_token_tag')) {
    function csrf_token_tag()
    {
        // Create new token and get its hashed value. Bad idea to give out the token directly.
        $token = new Token();
        $token_value = $token->getCsrfToken();
        // Return the html field with token value.
        return '<input type=\"hidden\" name=\"csrf_token\" value=\"' . Str::h(($token_value)) . '\">';
    }
}
