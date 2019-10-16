<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\Core\Model;
use Kikopolis\App\Framework\Orion\Interfaces\ModelInterface;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Post testing model
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class Post extends Model implements ModelInterface
{
    protected $fillable = ['id', 'title', 'category_id', 'category_title', 'body', 'tags', 'image_id', 'image', 'comment_count', 'author_id', 'author_name', 'created_at', 'modified_at', 'is_active', 'view_count'];

    protected $visible = [];

    protected $guarded = [];

    protected $hidden = [];

    public function __constructor()
    {
        //
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * Delete the model from the database.
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Increment a column by given amount, default 1.
     * @param string $column
     * @param int|float $amount
     * @return mixed
     */
    public function increment(string $column, $amount = 1)
    {
        // TODO: Implement increment() method.
    }

    /**
     * Decrement a column by given amount, default 1.
     * @param string $column
     * @param int $amount
     * @return mixed
     */
    public function decrement(string $column, $amount = 1)
    {
        // TODO: Implement decrement() method.
    }

    /**
     * Determine if the model uses timestamps.
     * @return mixed
     */
    public function hasTimeStamps()
    {
        // TODO: Implement hasTimeStamps() method.
    }

    /**
     * Set the timestamps of the model.
     * On create, set the created at and updated at.
     * On update, only set updated at.
     * @return mixed
     */
    public function setTimestamps()
    {
        // TODO: Implement setTimestamps() method.
    }
}
