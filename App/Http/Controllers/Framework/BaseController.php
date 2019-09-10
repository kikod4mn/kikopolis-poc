<?php

namespace Kikopolis\App\Http\Controllers\Framework;

use Kikopolis\App\Config\Config;

defined('_KIKOPOLIS') or die('No direct script access!');

abstract class BaseController
{
    public function redirect($page)
    {
        header('location: ' . Config::getUrlRoot() . '/' . $page, true, 303);
        exit;
    }
}
