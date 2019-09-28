<?php

namespace App\Models;

use Kikopolis\Core\Orion\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

class User extends Model
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

    protected $hidden = ['password_hash', 'password_reset_hash', 'password_reset_expires_at', 'activation_hash', 'remember_token_hash', 'remember_token_expires_at', 'is_active', 'is_disabled', 'status', 'api_key'];

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
    //     $this->query('SELECT * FROM users');
    //     return $this->resultSetClass();
    // }
}
