<?php

namespace App\Models;

class Post
{
    public $post;

    public function show()
    {
        return $this->post = [
            'title' => 'Title',
            'body' => 'Body',
            'slug' => 'slug-of-post'
        ];
    }
}