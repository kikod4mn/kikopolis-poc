<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\Core\Orion\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

class Post extends Model
{
    protected $fillable = ['id', 'title', 'category_id', 'category_title', 'body', 'tags', 'image_id', 'image', 'comment_count', 'author_id', 'author_name', 'created_at', 'modified_at', 'is_active', 'view_count'];

    protected $visible = [];

    protected $guarded = [];

    protected $hidden = [];

    public function __constructor()
    {
        //
    }

    public function insert(array $data) {
        return $this->save($data);
    }
}
