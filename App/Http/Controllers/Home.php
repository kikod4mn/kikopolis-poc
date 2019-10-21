<?php

declare(strict_types=1);

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Framework\Aurora\View;

/**
 * Home controller.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>v
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Home extends Controller
{
    /**
     * Pseudo constructor.
     */
    public function __constructor()
    {

    }

    public function indexAction()
    {
        View::render('home.index', []);
    }

    public function contactAction()
    {
        View::render('home.contact', []);
    }

    public function faqAction()
    {
        View::render('home.faq', []);
    }

    public function aboutAction()
    {
        View::render('home.about', []);
    }
}
