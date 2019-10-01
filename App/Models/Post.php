<?php

namespace App\Models;

use Kikopolis\Core\Orion\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

class Post extends Model
{
    protected $stmt;

    /**
     * Error messages
     *
     * @var array
     */
    protected $errors = [];

    public $attributes = [];

    protected $fillable = ['id', 'title', 'category_id', 'category_title', 'body', 'tags', 'image_id', 'image', 'comment_count', 'author_id', 'author_name', 'created_at', 'modified_at', 'is_active', 'view_count'];

    protected $visible = [];

    protected $guarded = [];

    protected $hidden = [];

    /**
     * Last inserted ID
     * 
     * @var int
     */
    public $lastInsertedId = null;

    public function __constructor()
    {
        //
    }

    // public function get()
    // {
    //     $this->query('SELECT * FROM posts');
    //     return $this->resultSet();
    // }

    public function show()
    {
        $this->post = [
            'title' => 'Title',
            'body' => 'Body',
            'slug' => 'slug-of-post'
        ];
        return $this->post;
    }
}
