<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\File\MimeArray;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * FileUpload upload methods.
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */

class FileUpload
{
    private $name = '';
    private $size = '';
    private $temp_name = '';
    private $temp_extension = [];
    private $extension = '';
    private $upload_dir = '';
    private $upload_dir_filename = '';
    private $upload_url_filename = '';
    private $allowed_mime_types = [];
    private $mime_type = '';
    private $mime_base = [];
	private $upload_url = '';

	/**
	 * FileUpload constructor.
	 * Default behaviour sorts all uploaded files by the upload year and month.
	 * @param        $file
	 * @param array  $allowed_exts
	 * @param string $upload_dir
	 * @param string $file_name
	 * @param bool   $random_name
	 * @throws \Exception random_bytes throws \Exception if no sufficient entropy was gathered.
	 */
    public function __construct($file, array $allowed_exts, string $upload_dir, bool $random_name = true, string $file_name = '')
    {
    	var_dump($file);
        $this->name               = $file['name'];
        $this->size               = $file['size'];
        $this->temp_name          = $file['tmp_name'];
        $this->temp_extension     = explode('.', $file['name']);
        $this->extension          = strtolower(end($this->temp_extension));
        $this->allowed_mime_types = $this->setMimeTypes($allowed_exts);
        $this->mime_base          = MimeArray::getMimeBase();
        $this->mime_type          = mime_content_type($file);
        $this->upload_dir         = sprintf("%s/public/uploads/%s/%s/%s/", Config::getAppRoot(), $this->formatDir($upload_dir), date('Y'), date('M'));
        $this->upload_url         = sprintf("%s/public/uploads/%s/%s/%s/", Config::getUrlRoot(), $this->formatDir($upload_dir), date('Y'), date('M'));
        if ($random_name === true && $file_name === '') {
            $this->upload_dir_filename = $this->upload_dir . $this->newRandomName();
            $this->upload_url_filename = $this->upload_url . $this->newRandomName();
        } elseif ($file_name !== '') {
			$this->upload_dir_filename = $this->upload_dir . $file_name . ".{$this->extension}";
			$this->upload_url_filename = $this->upload_url . $file_name . ".{$this->extension}";
		} else {
            $this->upload_dir_filename = $this->upload_dir . $this->name;
            $this->upload_url_filename = $this->upload_url . $this->name;
        }
    }

    /**
     * Remove possible harmful characters from the upload directory name.
     * @param string $dir
     * @return string
     */
    private function formatDir(string $dir): string
    {
        return Str::forbiddenChars($dir, [';', '<', '>', '`', '^', '"', '\'b']);
    }

    /**
     * Upload a file.
     * @return string
     * @throws \Exception
     */
    public function upload()
    {
        if (!$this->extension()) {
            throw new \Exception("File mime type or extension mismatch. {$this->mime_type} is not in the allowed array or {$this->extension} does not match the mime type.");
        }
        if (!$this->size()) {
            throw new \Exception("File size is too large. Maximum file size is 2MB.");
        }
        if (\file_exists($this->upload_dir_filename)) {
            throw new \Exception("File {$this->upload_dir_filename} already exists. Please use a randomized file name or rename the file.");
        }
		if (!\file_exists($this->upload_dir) && !\is_dir($this->upload_dir)) {
			if (!mkdir($this->upload_dir, 0777, true)) {
				throw new \Exception("Directory {$this->upload_dir} does not exist and cannot be created. Make sure the directory is writable.");
			}
		}
        if (!\is_writable($this->upload_dir)) {
            throw new \Exception("Error uploading file to directory {$this->upload_dir}. Check to see that the directory is writeable and exists.");
        } else {
            if (move_uploaded_file($this->temp_name, $this->upload_dir_filename)) {

                return $this->upload_dir_filename;
            } else {
                throw new \Exception("Error while moving file.");
            }
        }
    }

	/**
	 * Put the generated contents into the file. If it does not exist, creates it.
	 * Create the cache directory if it does not exist.
	 * @param string $file_contents
	 * @param string $dir
	 * @param string $file_name
	 * @return boolean
	 */
	public static function forceFileContents(string $file_contents, string $dir, string $file_name): bool
	{
		if (!file_exists($dir) || !is_dir($dir)) {
			mkdir($dir);
		}

		return (bool) file_put_contents($dir . '/' . $file_name, $file_contents);
	}

	/**
	 * Move uploaded file, if directory does not exist, make the directory.
	 * @param string $file_name
	 * @param string $dir
	 * @return bool
	 */
	public static function forceFileUpload(string $file_name, string $dir): bool
	{
		if (!file_exists($dir) || !is_dir($dir)) {
			mkdir($dir);
		}

		return (bool) move_uploaded_file($dir, $file_name);
	}

    /**
     * Check if file type matches file extension and is in the allowed mime array.
     * @return bool
     */
    private function extension() {
        foreach ($this->allowed_mime_types as $key => $mime) {
            if (!$this->mime($mime)) {

                return false;
            }

            if ($key !== $this->extension) {

                return false;
            }
        }
        return true;
    }

    /**
     * Check the mime type matches the current file type.
     * @param $mime
     * @return bool
     */
    private function mime($mime): bool
    {
        if (is_array($mime)) {
            foreach ($mime as $value) {
                if ($value === $this->mime_type) {

                    return true;
                }
            }

            return false;
        }

            return $mime === $this->mime_type;
    }

    /**
     * Set the allowed mime types array.
     * Fill it with full parameters from the mimes array.
     * @param $allowed_exts
     * @return array
     */
    private function setMimeTypes($allowed_exts): array
    {
        $allowed_mimes = [];
        foreach ($allowed_exts as $extension) {
            if (\array_key_exists($extension, $this->mime_base)) {
                $allowed_mimes[$extension] = $this->mime_base[$extension];
            }
        }

        return $allowed_mimes;
    }

    /**
     * Verify the file size is under the limit set in Config.
     * @return bool
     */
    private function size()
    {
        return Config::FILE_UPLOAD_SIZE > $this->size;
    }

    /**
     * Return a new random filename.
     * @return string
     * @throws \Exception random_bytes throws \Exception if no sufficient entropy was gathered.
     */
    private function newRandomName()
    {
        return sprintf("%s%s.%s", Str::random(12), uniqid(), $this->extension);
    }
}