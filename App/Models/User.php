<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\Core\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * User testing model
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class User extends Model
{
    protected $fillable = [
        'id',
        'name',
        'email',
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'is_active',
        'is_disabled',
        'account_level',
        'remember_token_hash',
        'remember_token_expires_at',
        'updated_at',
        'created_at'
    ];

    protected $visible = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at'
    ];

    protected $hidden = [
        'id',
        'name',
        'email',
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'is_active',
        'is_disabled',
        'account_level',
        'remember_token_hash',
        'remember_token_expires_at',
        'updated_at',
        'created_at'
    ];

    /**
     * Pseudo constructor
     */
    public function __constructor()
    {
        //
    }
}
