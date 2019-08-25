<?php

namespace App\Controllers\Http;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Controllers\More;
use App\Controllers\Show;

class Posts
{
    public function show(Show $show, More $more)
    {
        echo "<br><h1>Hi from posts</h1><br>";
        echo "<h4>The Get Array</h4>";
        var_dump(isset($_GET) ? $_GET : 'No get');
        echo "<br>";
        if ($show) {
            var_dump($show);
        }
        if ($more) {
            var_dump($more);
        }
    }
}
