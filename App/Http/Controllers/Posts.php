<?php

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use App\Http\Controllers\More;
use App\Http\Controllers\Show;
use Kikopolis\App\Config\Config;

class Posts
{
    public $show;
    public $more;
    public $post;
    public $config;

    public function __construct(Show $show, More $more, Post $post, Config $config)
    {
        echo "<br>The Posts Constructor<br>";
        $this->show = $show;
        $this->more = $more;
        $this->post = $post;
        $this->config = $config;
        echo "<h4>The Get Array</h4>";
        var_dump(!empty($_GET) ? $_GET : 'No get');
        echo "<br>";
        echo "<h4>The Post Array</h4>";
        var_dump(!empty($_POST) ? $_POST : 'No post');
        echo "<br>";
    }

    /**
     * Show posts
     *
     * @param Show $show
     * @param More $more
     * @return void
     */
    public function show()
    {
        echo "<br><h1>Hi from posts</h1><br>";
        if ($this->show) {
            var_dump($this->show->me());
        }
        if ($this->more) {
            var_dump($this->more->options());
        }
        print_r($this->post->show());
        var_dump($this->config->getAppRoot());
    }
}
