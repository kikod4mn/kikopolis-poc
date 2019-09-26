<?php

namespace App\Models;

use Kikopolis\Core\Database;

defined('_KIKOPOLIS') or die('No direct script access!');

class User extends Database
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

    protected $hidden = [];

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
        $this->query('SELECT * FROM users');
        return $this->resultSetClass();
    }
}
