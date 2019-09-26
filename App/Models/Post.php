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

    protected $fillable = [];

    protected $visible = [];

    protected $guarded = [];

    protected $hidden = [];

    /**
     * Last inserted ID
     * 
     * @var int
     */
    public $lastInsertedId = null;

    public function __child_construct()
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
