<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Models\User;
use Kikopolis\App\Auth\Auth;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * AdminMiddleware
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class AdminMiddleware
{
	public static function middleware()
	{
		$user = new User();

		return Auth::admin($user);
	}
}