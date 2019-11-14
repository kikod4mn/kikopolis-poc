<?php

declare(strict_types=1);

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
    }
}

if (!function_exists('withMessage')) {
    function withMessage(string $message, string $type = 'alert-success', string $title = 'Notification')
    {
        $_SESSION['flash'][] = [
            'title' => $title,
            'body' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('returnTo')) {
    function returnTo(){
    	if ($_SESSION['previous_page'] === $_SERVER['query_string']) {
			$_SESSION['previous_page'] = 'home';
		}
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
    }
}

if (!function_exists('getFlashMessages')) {
    function getFlashMessages() {
        if (isset($_SESSION['flash'])) {
            $messages = $_SESSION['flash'];
            unset($_SESSION['flash']);

            return $messages;
        }

        return false;
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
