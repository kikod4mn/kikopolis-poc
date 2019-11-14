<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Http\Controllers\Authorization\Register;
use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * RegisterMiddleware.php
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class RegisterMiddleware
{
    public static function middleware()
    {
        if (Config::ALLOW_REGISTRATION === false) {
            return false;
        }
        return true;
    }
}