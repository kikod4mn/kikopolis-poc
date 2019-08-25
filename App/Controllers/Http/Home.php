<?php

namespace App\Controllers\Http;

class Home
{
    public function index(Show $show, More $more)
    {
        echo "<br><h1>Hi, cruel world of PHP</h1><br>";
        if ($show) {
            var_dump($show);
        }
        if ($more) {
            var_dump($more);
        }
    }
}