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

    public $attributes = [];

    protected $fillable = ['id', 'first_name', 'last_name', 'email', 'image', 'phone_number', 'gender', 'date_of_birth', 'street', 'house_or_apartment', 'city', 'state_or_province', 'post_code', 'country', 'created_at'];

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
}
