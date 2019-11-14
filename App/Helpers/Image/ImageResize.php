<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers\Image;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ImageResize
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class ImageResize
{
	/**
	 * Properties of class
	 */
	protected $images = [];
	protected $source;
	protected $mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
	protected $invalid = [];
	protected $output_sizes = [];
	protected $use_longer_dimension;
	protected $jpeg_quality = 75;
	protected $png_compression = 0;
	protected $resample = IMG_BILINEAR_FIXED;
	protected $watermark;
	protected $mark_width;
	protected $mark_height;
	protected $mark_type;
	protected $margin_right;
	protected $margin_bottom;
	protected $destination;
	protected $generated = [];

	/**
	 * Class constructor
	 * @param array $images
	 * @param null  $source_directory
	 */
	public function __construct(array $images, $source_directory = null)
	{
		if (!is_null($source_directory) && !is_dir($source_directory)) {
			Flash::addMessage('Source directory is not a directory.', 'alert-danger');
			return false;
		}
		$this->images = $images;
		$this->source = $source_directory;
		$this->checkImages();
	}

	/**
	 * Set output sizes
	 *
	 * @param array $sizes An array of output sizes to which the image will be converted
	 * @param boolean $use_longer_dimension Optional parameter to use the longer side of the image for scaling
	 *
	 * @return void|boolean
	 */
	public function setOutputSizes(array $sizes, $use_longer_dimension = true)
	{
		foreach ($sizes as $size) {
			if (!is_numeric($size) || $size <= 0) {
				Flash::addMessage('Sizes must be an array of positive numbers', 'alert-danger');
				return false;
			}
			$this->output_sizes[] = (int) $size;
		}
		$this->use_longer_dimension = $use_longer_dimension;
		if (!$this->use_imagescale) {
			$this->calculateRatios();
		}
	}

	/**
	 * Set the JPG quality
	 *
	 * @param int $number The JPG quality number, must be between 0 and 100
	 *
	 * @return void
	 */
	public function setJpgQuality($number)
	{
		if (!is_numeric($number) || $number < 0 || $number > 100) {
			Flash::addMessage('Quality must be in the range of 0-100.', 'alert-danger');
			return false;
		}
		$this->jpeg_quality = $number;
	}

	/**
	 * Set the PNG compression
	 *
	 * @param int $number The compression amount, must be between 0 and 9
	 *
	 * @return void
	 */
	public function setPngCompression($number)
	{
		if (!is_numeric($number) || $number < 0 || $number > 9) {
			Flash::addMessage('Compression must be in the range of 0-9', 'alert-danger');
			return false;
		}
		$this->png_compression = $number;
	}

	/**
	 * Use this to change the resampling method
	 *
	 * @param string $value The name of resampling method for JPG
	 *
	 * @return void
	 */
	public function setResamplingMethod($value)
	{
		switch (strtolower($value)) {
			case 'bicubic':
				$this->resample = IMG_BICUBIC;
				break;
			case 'bicubic-fixed':
				$this->resample = IMG_BICUBIC_FIXED;
				break;
			case 'nearest-neighbour':
			case 'nearest-neighbor':
				$this->resample = IMG_NEAREST_NEIGHBOUR;
				break;
			default:
				$this->resample = IMG_BILINEAR_FIXED;
		}
	}

	/**
	 * Set the watermark image and offset from the image corner
	 *
	 * @param string $filepath The path to the watermark image
	 * @param int $margin_right The margin from the right side of the image
	 * @param int $margin_left The margin from the bottom of the image
	 *
	 * @return void
	 */
	public function setWatermark($filepath, $margin_Right = 30, $margin_Bottom = 30)
	{
		if (!file_exists($filepath) || !is_readable($filepath)) {
			Flash::addMessage('Cannot access the watermark image. Please try again later or if the problem persists, contact the administrator', 'alert-danger');
			return false;
		}
		$size = getimagesize($filepath);
		if ($size === false && $this->webp_supported && mime_content_type($filepath) == 'image/webp') {
			$size['mime'] = 'image/webp';
		}

		if (!in_array($size['mime'], $this->mime_types)) {
			Flash::addMessage('Watermark must be one of the following types: ' . implode(', ', $this->mime_types), 'alert-danger');
			return false;
		}
		$this->watermark = $this->createImageResource($filepath, $size['mime']);

		if ($size['mime'] == 'image/webp') {
			$this->mark_width = imagesx($this->watermark);
			$this->mark_height = imagesy($this->watermark);
		} else {
			$this->mark_width = $size[0];
			$this->mark_height = $size[1];
		}

		if (is_numeric($margin_Right) && $margin_Right > 0) {
			$this->margin_right = $margin_Right;
		}

		if (is_numeric($margin_Bottom) && $margin_Bottom > 0) {
			$this->margin_bottom = $margin_Bottom;
		}
	}

	/**
	 * Output the resized and watermarked images
	 *
	 * @param string $destination The output folder
	 *
	 * @return array An array of generated output and invalid files
	 */
	public function outputImages($destination)
	{
		if (!is_dir($destination) || !is_writable($destination)) {
			Flash::addMessage('Destination folder does not have the correct write permissions or is not a directory. Try again later and if the problem persists, contact Admin.', 'alert-danger');
			return false;
		}
		$this->destination = $destination;
		// Loop through the source images
		foreach ($this->images as $i => $img) {
			// Skip invalid files
			if (in_array($this->images[$i]['file'], $this->invalid)) {
				continue;
			}
			// Create image resource for the current image
			$resource = $this->createImageResource($this->images[$i]['file'], $this->images[$i]['type']);
			// Add a watermark if the watermark property contains a value
			if ($this->watermark) {
				$this->addWatermark($this->images[$i], $resource);
			}
			// Delegate the generation of output to another method
			$this->generateOutput($this->images[$i], $resource);
			imagedestroy($resource);
		}
		// Return arrays of output and invalid files
		return ['output' => $this->generated, 'invalid' => $this->invalid];
	}

	/**
	 * Protected check images function
	 *
	 * @return void
	 */
	protected function checkImages()
	{
		foreach ($this->images as $i => $image) {
			$this->images[$i] = [];
			if ($this->source) {
				$this->images[$i]['file'] = $this->source . DIRECTORY_SEPARATOR . $image;
			} else {
				$this->images[$i]['file'] = $image;
			}
			if (file_exists($this->images[$i]['file']) && is_readable($this->images[$i]['file'])) {
				$size = getimagesize($this->images[$i]['file']);

				if ($size === false && $this->webp_supported && mime_content_type($this->images[$i]['file']) == 'image/webp') {
					$this->images[$i] = $this->getWebpDetails($this->images[$i]['file']);
				} elseif ($size[0] === 0 || !in_array($size['mime'], $this->mime_types)) {
					$this->invalid[] = $this->images[$i]['file'];
				} else {
					if ($size['mime'] == 'image/jpeg') {
						$results = $this->checkJpgOrientation($this->images[$i]['file'], $size);
						$this->images[$i]['file'] = $results['file'];
						$size = $results['size'];
					}
					$this->images[$i]['w'] = $size[0];
					$this->images[$i]['h'] = $size[1];
					$this->images[$i]['type'] = $size['mime'];
				}
			} else {
				$this->invalid[] = $this->images[$i]['file'];
			}
		}
	}

	/**
	 * Get details of webp
	 *
	 * @param image $image The image in WebP format
	 *
	 * @return array $details The array of webp details
	 */
	protected function getWebpDetails($image)
	{
		$details = [];
		$resource = imagecreatefromwebp($image);
		$details['file'] = $image;
		$details['w'] = imagesx($resource);
		$details['h'] = imagesy($resource);
		$details['type'] = 'image/webp';
		imagedestroy($resource);
		return $details;
	}

	/**
	 * Check the jpg orientation
	 *
	 * @param image $image The JPG to check landscape or portrait orientation
	 * @param array $size  The size array of the image
	 *
	 * @return array
	 */
	protected function checkJpgOrientation($image, $size)
	{
		$angle = null;
		$output_file = $image;
		$exif = exif_read_data($image);
		if (!empty($exif['Orientation'])) {
			switch ($exif['Orientation']) {
				case 3:
					$angle = 180;
					break;
				case 6:
					$angle = -90;
					break;
				case 8:
					$angle = 90;
					break;
				default:
					$angle = null;
			}
		}
		if (!is_null($angle)) {
			$original = imagecreatefromjpeg($image);
			$rotated = imagerotate($original, $angle, 0);
			$extension = pathinfo($image, PATHINFO_EXTENSION);
			$output_file = str_replace(".$extension", '_rotated.jpg', $image);
			imagejpeg($rotated, $output_file, 100);
			$size = getimagesize($output_file);
			imagedestroy($original);
			imagedestroy($rotated);
		}
		return ['file' => $output_file, 'size' => $size];
	}

	/**
	 * Calculate ratios for resizing
	 *
	 * @return void
	 */
	protected function calculateRatios()
	{
		foreach ($this->images as $i => $image) {
			$this->images[$i]['ratios'] = [];
			if ($this->images[$i]['h'] > $this->images[$i]['w'] && $this->use_longer_dimension) {
				$divisor = $this->images[$i]['h'];
			} else {
				$divisor = $this->images[$i]['w'];
			}
			foreach ($this->output_sizes as $output_size) {
				$ratio = $output_size / $divisor;
				$this->images[$i]['ratios'][] = $ratio > 1 ? 1 : $ratio;
			}
		}
	}

	/**
	 * Create an image resource
	 *
	 * @param image $file Base image to create a resource from
	 * @param string $type The mime type of the image
	 *
	 * @return image $file The image created from the resources
	 */
	protected function createImageResource($file, $type)
	{
		switch ($type) {
			case 'image/jpeg':
				return imagecreatefromjpeg($file);
				break;
			case 'image/png':
				return imagecreatefrompng($file);
				break;
			case 'image/gif':
				return imagecreatefromgif($file);
				break;
			case 'image/webp':
				return imagecreatefromwebp($file);
				break;
		}
	}

	/**
	 * Add the Watermark to the main image resource
	 *
	 * @param array $image An array that contains dimensions and mime type of the current image
	 * @param image $resource The image resource from createImageResource
	 *
	 * @return void
	 */
	protected function addWatermark(array $image, $resource)
	{
		$x = $image['w'] - $this->mark_width - $this->margin_bottom;
		$y = $image['h'] - $this->mark_height - $this->margin_right;

		imagecopy($resource, $this->watermark, $x, $y, 0, 0, $this->mark_width, $this->mark_height);
	}

	/**
	 * Generate the output files
	 *
	 * @param array $image An array of the current image properties
	 * @param image $resource
	 *
	 * @return void
	 */
	protected function generateOutput($image, $resource)
	{
		// Store output sizes in a temp variable
		$stored_sizes = $this->output_sizes;
		// Get the parts of the current filename
		$name_parts = pathinfo($image['file']);
		// Recalculate $output_sizes if the images height is greater than width
		if ($this->use_longer_dimension && imagesy($resource) > imagesx($resource)) {
			$this->recalculateSizes($resource);
		}
		foreach ($this->output_sizes as $output_size) {
			// Dont resize if current output is greater than original file
			if ($output_size >= $image['w']) {
				continue;
			}
			$scaled = imagescale($resource, $output_size, -1, $this->resample);
			$filename = $name_parts['filename'] . '_' . $output_size . '.' . $name_parts['extension'];
			// Delegate file output to specialized method
			$this->outputFile($scaled, $image['type'], $filename);
		}
		$this->output_sizes = $stored_sizes;
	}

	/**
	 * Recalculate the file sizes
	 *
	 * @param image $resource The file to recalculate
	 *
	 * @return void
	 */
	protected function recalculateSizes($resource)
	{
		// Get the width and height of the image resource
		$w = imagesx($resource);
		$h = imagesy($resource);
		foreach ($this->output_sizes as &$size) {
			// Multiply the size by the width divided by the height
			// Second argument of round() -1 rounds to the nearest multiple of 10
			$size = round($size * $w / $h, -1);
		}
	}

	/**
	 * Output the file to the destination
	 *
	 * @param image  $scaled_final_image The scaled image
	 * @param string $type               The file mime type
	 * @param string $name               The name of the file
	 *
	 * @return voic
	 */
	protected function outputFile($scaled_final_image, $type, $name)
	{
		$success = false;
		$output_file = $this->destination . DIRECTORY_SEPARATOR . $name;
		switch ($type) {
			case 'image/jpeg':
				$success = imagejpeg($scaled_final_image, $output_file, $this->jpeg_quality);
				break;
			case 'image/png':
				$success = imagepng($scaled_final_image, $output_file, $this->png_compression);
				break;
			case 'image/gif':
				$success = imagegif($scaled_final_image, $output_file);
				break;
			case 'image/webp':
				$success = imagewebp($scaled_final_image, $output_file);
				break;
		}
		imagedestroy($scaled_final_image);
		if ($success) {
			$this->generated[] = $output_file;
		}
	}
}