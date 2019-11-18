<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contact as ContactModel;
use Kikopolis\App\Config\Config;
use Kikopolis\App\Framework\Aurora\View;
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
    /**
     * @param Request $request
     * @param ContactModel $contact
     * @return void
     * @throws \Exception
     */
    public function sendEmailAction(Request $request, ContactModel $contact)
    {
        $msg = $request->query();
        if (!$contact->validateDataArray($msg)) {
            throw new \Exception("Invalid form submission.");
        }
        $mail = new Mail(Config::ADMIN_EMAIL, $msg['subject'], $msg['message']);
        $result = $mail->send();
        if ($result) {
            return redirect('email-success');
        }
        withMessage('Email not sent. Please wait and try again.', 'alert-danger');
        return returnTo();
    }

    public function sendSuccessAction()
    {
        return View::render('home.email-success');
    }
}