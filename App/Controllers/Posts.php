<?php

namespace App\Controllers;

class Posts
{
    public function show(Post $post)
    {
        echo "Hi from posts";
        var_dump($post->slug);
    }
}