<?php

namespace App\Controllers\Http;

class Posts
{
    public function show(Post $post)
    {
        echo $_GET['id'];
        echo $_GET['title'];
        echo "Hi from posts";
        var_dump($post->slug);
    }
}