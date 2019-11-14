<?php

declare(strict_types=1);

namespace App\Models\Framework;

use Kikopolis\Core\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Content
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Content extends Model
{
    /**
     * Put mass fillable model properties in this array.
     * @var array
     */
    protected $fillable = ['page_route', 'content'];

    /**
     * Put model properties that should remain hidden in this array.
     * @var array
     */
    protected $hidden = [];

    /**
     * Save the model to the database.
     * @param array $data
     * @return bool|int Returns false on failure or last inserted id on success.
     * @throws \Exception
     */
    public function save(array $data)
    {
		$data = $this->processContent($data);

        return $this->insert($data);
    }

	private function processContent($data)
	{
		// Markdown conversion goes here

		return $data;
    }

    /**
     * Update the model in the database.
     * Id must be in the $data array to find the db entry to modify.
     * @param $data
     * @return bool|int Returns false on failure or last modified id on success
     * @throws \Exception
     */
    public function update($data)
    {
		$data = $this->processContent($data);
		var_dump($data);
		die();
        return $this->modify($data);
    }

    /**
     * Delete a model in the database.
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }
}