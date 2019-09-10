<?php

namespace App\Http\Controllers;

use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

class More
{
    public $options;

    public function __construct()
    {
        echo "<br>The More Constructor<br>";
    }

    public function options()
    {
        $this->options = ([
            'title' => "Options from the More class<br>",
            'var1' => rand(),
            'var2' => Str::randomString(16)
        ]);
        return $this;
    }
}
