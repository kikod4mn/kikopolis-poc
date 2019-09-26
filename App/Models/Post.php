<?php

namespace App\Models;

use App\Models\User;
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

    public function __construct($data = [], User $user)
    {
        $this->db = $this->getDb();
        // var_dump($user->get());
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
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
