<?php declare(strict_types=1);

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;
use Kikopolis\App\Utility\Token;

/**
 * Misc helper functions.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

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
