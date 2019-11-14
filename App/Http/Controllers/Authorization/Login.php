<?php

declare(strict_types=1);

namespace App\Http\Controllers\Authorization;

use App\Models\User;
use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Utility\Token;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Login
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Login extends Controller
{
    protected static $expired = true;

    public static function expired()
    {
        return self::$expired;
    }

    public static function login(User $user, Token $token)
    {
        session_regenerate_id(true);
        if (!$user->findByKey($_SESSION['user_id'])) {
            $cookie = self::cookieToken();
        }
        if (!isset($cookie)) {

            return false;
        }
        $_SESSION['user_id'] = 4;
        self::newToken($token);
        var_dump($user);
    }

    private static function cookieToken()
    {
        if (!isset($_COOKIE['remember'])) {

            return false;
        }
        if (!Login::findByToken($_COOKIE['remember'])) {

            return false;
        }
        if (Login::expired()) {

            return false;
        }
        return $_COOKIE['remember'];
    }

    private static function newToken(Token $token)
    {
        $db_token = $token->getToken();
        $_SESSION['remember'] = $token->getTokenHash();
        var_dump($db_token);
        var_dump($_SESSION['remember']);
    }

    // TODO: Find user from the database by the remember me hashed token
    public static function findByToken(string $token)
    {
        if (1 > 2) {
            // If login not expired
            self::$expired = false;
        }
        return true;
    }
}