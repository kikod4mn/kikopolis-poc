<?php

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use App\Http\Controllers\More;
use App\Http\Controllers\Show;
use Kikopolis\App\Config\Config;
use Kikopolis\App\Http\Controllers\Controller;

class Posts extends Controller
{
    public $show;
    public $more;
    public $post;
    public $config;
    protected $params;

    public function __construct(Show $show, More $more, Post $post, Config $config)
    {
        // Get the route parameters from the base controller
        $this->params = Controller::getRouteParams();
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
        var_dump($this->params);
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
        if (isset($this->params['id'])) {
            echo "<h1>ID IS SET - </h1>" . $this->params['id'];
        }
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