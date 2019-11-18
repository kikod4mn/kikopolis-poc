<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Framework\Mail\Mail;
use Kikopolis\Core\Http\Request;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Mail
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Contact extends Controller
{
    public function sendEmailAction(Request $request)
    {
        $msg = $request->query();
        $mail = new Mail(Config::ADMIN_EMAIL, $msg['subject'], $msg['message']);
        var_dump($mail->send());
    }
}