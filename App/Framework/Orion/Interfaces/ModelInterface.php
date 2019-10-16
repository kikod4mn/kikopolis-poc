<?php declare(strict_types=1);

namespace Kikopolis\App\Framework\Orion\Interfaces;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ModelInterface
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

interface ModelInterface
{
    /**
     * Returns all results found in the specified columns. Default is all columns.
     * @param int $limit
     * @param array $columns
     * @return object
     */
    public function get(int $limit = 0, array $columns = ['*']);

    /**
     * Find and return a database row with a provided key.
     * @param $key
     * @return mixed
     */
    public function find($key);

    /**
     * Save the model to the database.
     * @param array $data
     * @return mixed $lastInsertedId
     */
    public function save(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function update(array $data);

    /**
     * Delete the model from the database.
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Increment a column by given amount, default 1.
     * @param string $column
     * @param int|float $amount
     * @return mixed
     */
    public function increment(string $column, $amount = 1);

    /**
     * Decrement a column by given amount, default 1.
     * @param string $column
     * @param int $amount
     * @return mixed
     */
    public function decrement(string $column, $amount = 1);

    /**
     * Determine if the model uses timestamps.
     * @return mixed
     */
    public function hasTimeStamps();

    /**
     * Set the timestamps of the model.
     * On create, set the created at and updated at.
     * On update, only set updated at.
     * @return mixed
     */
    public function setTimestamps();







}
