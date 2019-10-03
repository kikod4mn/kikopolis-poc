<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\Core\Orion\Model;

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
        'first_name',
        'last_name',
        'email',
        'image',
        'phone_number',
        'gender',
        'date_of_birth',
        'street',
        'house_or_apartment',
        'city',
        'state_or_province',
        'post_code',
        'country',
        'created_at',
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'remember_token_hash',
        'remember_token_expires_at',
        'is_active',
        'is_disabled',
        'status',
        'api_key'
    ];

    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'email',
        'image',
        'phone_number',
        'gender',
        'date_of_birth',
        'street',
        'house_or_apartment',
        'city',
        'state_or_province',
        'post_code',
        'country',
        'created_at'
    ];

    protected $guarded = [];

    protected $hidden = [
        'password_hash',
        'password_reset_hash',
        'password_reset_expires_at',
        'activation_hash',
        'remember_token_hash',
        'remember_token_expires_at',
        'is_active',
        'is_disabled',
        'status',
        'api_key'
    ];

    public function __constructor()
    {
        //
    }

//    public function insert(array $data) {
//        if(isset($data['password_hash'])) {
//            $data['password_hash'] = password_hash($data['password_hash'], PASSWORD_DEFAULT);
//        }
//        return $this->save($data);
//    }
}
