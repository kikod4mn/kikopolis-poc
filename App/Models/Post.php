<?php

namespace App\Models;

class Post
{
    public function show()
    {
        return $post = [
            'title' => 'Title',
            'body' => 'Body',
            'slug' => 'slug-of-post'
        ];
    }
}