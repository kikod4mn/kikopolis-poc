<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Admin
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Admin extends Controller
{
	protected $middleware = ['auth', 'admin'];

    public function indexAction()
    {
        return View::render('admin.index', []);
    }
}