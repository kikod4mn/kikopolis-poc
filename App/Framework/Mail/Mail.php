<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Mail;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;
use PHPMailer\PHPMailer\PHPMailer;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Mail
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Mail
{
    private $mailer;
    private $to = '';
    private $subject = '';
    private $message = '';
    private $from = '';

    public function __construct($to, $subject, $message)
    {
        $this->mailer = new PHPMailer();
        $this->to = $to;
//        $this->from = $from;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function send()
    {
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->IsSMTP();
        $this->mailer->Host = Config::PHPMAILER_HOST;
        $this->mailer->SMTPSecure = Config::PHPMAILER_SMTP_SECURE;
        $this->mailer->Port = Config::PHPMAILER_PORT;
        $this->mailer->SMTPDebug = 4;
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPAutoTLS = false;
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );
        $this->mailer->Username = Config::PHPMAILER_UNAME;
        $this->mailer->Password = Config::PHPMAILER_PWD;
        $this->mailer->SetFrom('no-reply@kikopolis.tech', 'no-reply');
//        $this->mailer->AddReplyTo('no-reply@mycomp.com','no-reply');
        $this->mailer->Subject = Str::h($this->subject);
        $this->mailer->MsgHTML(Str::h($this->message));
        $this->mailer->AddAddress(Str::email($this->to));
//        $this->mailer->AddAttachment($fileName);
        return $this->mailer->send();
    }
}