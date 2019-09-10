<?php

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

class Show
{
    public $me;

    public function __construct()
    {
        echo "<br>The Show Constructor<br>";
    }

    public function me()
    {
        $this->me = "Me from Show class<br>";
        return $this;
    }
}
