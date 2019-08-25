<?php

namespace App\Controllers;

class Show
{
    public $me;

    public function Me()
    {
        $this->me = "Me from Show class<br>";
        return $this;
    }
}