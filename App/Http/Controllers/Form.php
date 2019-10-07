<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Kikopolis\Core\Http\Request;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Form
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Form
{
    public function display(Request $request)
    {
        var_dump(Request::createFromGlobals());
        var_dump($request);
        var_dump($_POST);
    }
}