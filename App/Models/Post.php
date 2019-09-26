<?php

namespace App\Models;

use Kikopolis\Core\Database;

defined('_KIKOPOLIS') or die('No direct script access!');

class Post extends Database
{
    protected $stmt;

    /**
     * Error messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Last inserted ID
     * 
     * @var int
     */
    public $lastInsertedId = null;

    public function __construct($data = [])
    {
        $this->db = $this->getDb();

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get()
    {
        $this->query('SELECT * FROM posts');
        return $this->resultSet();
    }

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
