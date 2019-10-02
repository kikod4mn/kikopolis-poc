<?php

declare(strict_types=1);

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use Kikopolis\App\Framework\Controllers\Controller;

class Posts extends Controller
{
    /**
     * Parameter bag from the GET array
     *
     * @var array
     */
    protected $params = [];

    public function __construct()
    {
        // Get the route parameters from the base controller.
        // This line is necessary in all controllers that utilize a parameter from the url.
        $this->params = Controller::getRouteParams();
    }

    /**
     * Show posts
     * @param Post $post
     * @return void
     * @throws \Exception
     */
    public function show(Post $post)
    {
        var_dump($post->get());
        echo "<h1>The id of the post is {$this->params['id']}</h1>";
        echo "<h1>The slug of the post is {$this->params['slug']}</h1>";
        echo "Well done on reaching here, young padawan!!";
    }
}
