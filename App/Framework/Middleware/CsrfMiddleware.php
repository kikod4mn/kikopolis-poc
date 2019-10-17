<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use Kikopolis\App\Utility\Token;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * CsrfMiddleware
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class CsrfMiddleware
{
    public static function middleware()
    {
        $token = new Token();

        return $token->csrfTokenIsValid($_POST['csrf'], $_SESSION['csrf_token']);
    }
}