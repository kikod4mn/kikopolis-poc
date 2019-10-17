<?php

declare(strict_types=1);

namespace Kikopolis\App\Auth;

use App\Http\Controllers\Authorization\Login;
use App\Models\User;
use Kikopolis\App\Config\Config;
use Kikopolis\App\Utility\Token;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Auth
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Auth
{
    public static function user(User $user)
    {
        if (isset($_SESSION['user_id'])) {
            $user_test = $user->find($_SESSION['user_id']);
            var_dump($user_test);
            return $user_test;
        }
        echo "false";
        return false;
    }
    
    /**
     * Remember the previously requested page to redirect after login.
     * @return void
     */
    public static function rememberPage()
    {
        $url_root = Config::getUrlRoot();
        $url_to_return = $_SERVER['REQUEST_URI'];
        $page = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $url_root);
        $page = str_replace($page, '', $url_to_return);
        $page = ltrim($page, '/');
        $_SESSION['return_to'] = $page;
    }

    /**
     * Get the saved page for redirection after login or to homepage as default.
     * @return string
     */
    public static function getRememberedPage()
    {
        if (isset($_SESSION['return_to'])) {
            $page = filter_var($_SESSION['return_to'], FILTER_SANITIZE_STRING);
            unset($_SESSION['return_to']);
        } else {
            $page = '';
        }

        return $page;
    }
}