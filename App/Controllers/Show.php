<?php

namespace App\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Show
{
    public $me;

    public function me()
    {
        $this->me = "Me from Show class<br>";
        return $this;
    }
}