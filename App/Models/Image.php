<?php

declare(strict_types=1);

namespace App\Models;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\FileUpload;
use Kikopolis\App\Helpers\Image\ImageResize;
use Kikopolis\App\Helpers\Image\ResizeImages;
use Kikopolis\App\Helpers\Str;
use Kikopolis\App\Utility\Validate;
use Kikopolis\Core\Model;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Image
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Image extends Model
{
    /**
     * Put mass fillable model properties in this array.
     * @var array
     */
    protected $fillable = ['title', 'category', 'slug', 'description', 'size_s', 'size_m', 'size_l', 'size_xl', 'size_original', 'enable_gallery', 'for_post'];

    /**
     * Put model properties that should remain hidden in this array.
     * @var array
     */
    protected $hidden = [];

    protected $allowed_exts = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    /**
     * Save the model to the database.
     * @param array $data
     * @return bool|int Returns false on failure or last inserted id on success.
     * @throws \Exception
     */
    public function save(array $data)
    {
    	$data = $this->validate($data);
    	$image_upload = new FileUpload($_FILES['image_file'], $this->allowed_exts, 'images', true);
    	$new_file = $image_upload->upload();
    	$data['size_original'] = Config::getUrlRoot() . str_replace(Config::getAppRoot(), '', $new_file);
    	$image_resize = ResizeImages::resizeAndOutput($new_file, [150, 400, 600, 900], Config::getAppRoot() . "/public/images/watermark.png");
		$data = $this->setImageSizes($data, $image_resize['output']);

    	return $this->insert($data);
    }

	private function validate($data)
	{
		if ($data['title'] === '' || $data['description'] === '' || $data['category'] === '' || !isset($_FILES['image_file'])) {
			withMessage("Title, category, description and an image file are required attributes.", 'alert-danger');
			returnTo();
		}
		$data['slug'] = Str::slug($data['title']);
		if (!isset($data['enable_gallery']) || (bool) $data['enable_gallery'] === false) {
			$data['enable_gallery'] = 1;
		}

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
		$data = $this->validate($data);
		$image_upload = new FileUpload($_FILES['image_file'], $this->allowed_exts, 'images', true);
		$new_file = $image_upload->upload();
		$data['size_original'] = $new_file;
		$image_resize = ResizeImages::resizeAndOutput($new_file, [150, 400, 600, 900], Config::getAppRoot() . "/public/images/watermark.png");
		$data = $this->setImageSizes($data, $image_resize['output']);

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

	private function setImageSizes($data, $output)
	{
		foreach ($output as $key => $value) {
			if ($key == 150) {
				$data['size_s'] = $value;
			}
			if ($key == 400) {
				$data['size_m'] = $value;
			}
			if ($key == 600) {
				$data['size_l'] = $value;
			}
			if ($key == 900) {
				$data['size_xl'] = $value;
			}
		}
		return $data;
	}
}