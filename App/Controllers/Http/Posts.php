<?php

namespace App\Controllers\Http;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use App\Controllers\More;
use App\Controllers\Show;
use Kikopolis\App\Config\Config;

class Posts
{
    public $show;
    public $more;
    public $post;
    public $config;

    public function __construct(Show $show, More $more, Post $post, Config $config)
    {
        $this->show = $show;
        $this->more = $more;
        $this->post = $post;
        $this->config = $config;
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
        echo "<h4>The Get Array</h4>";
        var_dump(isset($_GET) ? $_GET : 'No get');
        echo "<br>";
        if ($this->show) {
            var_dump($this->show->Me());
        }
        if ($this->more) {
            var_dump($this->more->options());
        }
        print_r($this->post->show());
        var_dump($this->config->getAppRoot());
    }
}