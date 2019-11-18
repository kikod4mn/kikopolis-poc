<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Kikopolis\App\Framework\Aurora\View;
use Kikopolis\App\Framework\Controllers\Controller;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Documentation
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Documentation extends Controller
{
    public function indexAction()
    {
        return View::render('docs.index');
    }

    public function showAction()
    {
        return View::render('docs.show', ['slug' => $this->params['slug']]);
    }
}