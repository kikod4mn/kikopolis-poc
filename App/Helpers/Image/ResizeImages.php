<?php

declare(strict_types=1);

namespace Kikopolis\App\Helpers\Image;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * ResizeImages
 * Part of the Kikopolis MVC Framework.
 * @author  Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class ResizeImages
{
	private $raw_image = '';
	private $raw_image_with_data = [];
	private $src_dir = '';
	private $mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
	private $invalid = [];
	private $output_sizes = [];
	private $use_longer_dimension = true;
	private $jpeg_quality = 75;
	private $png_compression = 0;
	private $resample = IMG_BILINEAR_FIXED;
	private $watermark = null;
	private $mark_width = null;
	private $mark_height = null;
	private $mark_type = null;
	private $margin_right = 0;
	private $margin_bottom = 0;
	private $upload_dir = '';
	private $db_dir = '';
	private $generated = [];

	/**
	 * ResizeImages constructor.
	 * @param string $image_path
	 * @param bool   $use_src_dir
	 * @param string $destination_dir
	 */
	public function __construct(string $image_path, bool $use_src_dir, string $destination_dir = '')
	{
		$this->raw_image = $image_path;
		if ($use_src_dir === true && $destination_dir === '') {
			$this->src_dir    = Str::removeFileFromPath($this->raw_image);
			$this->upload_dir = $this->src_dir;
			$this->db_dir = str_replace(Config::getAppRoot(), '', $this->upload_dir);
		} elseif ($use_src_dir === false && $destination_dir !== '') {
			$this->src_dir    = $destination_dir;
			$this->upload_dir = $destination_dir;
			$this->db_dir = str_replace(Config::getAppRoot(), '', $this->upload_dir);
		} else {
			withMessage("You must either set $use_src_dir to true to use the images old source dir for uploading or specify a destination directory.");
			returnTo();
		}
	}

	/**
	 * Main input and output method for an image to be resized.
	 * Also watermarks the original file if a watermark is provided.
	 * @param string      $image_path              The path to the image in the filesystem.
	 * @param array       $sizes                   Array of desired sizes.
	 * @param string      $watermark_file          The path to the watermark image in the filesystem.
	 * @param bool        $use_src_dir             Use the source directory as the destination. Overwrites already
	 *                                             present files. Default true.
	 * @param string      $destination_dir         Use this to specify a different destination dir other than source.
	 * @param bool        $use_longer_dimension    Use the longer dimension of the image to help maintain aspect
	 *                                             ration. Default true.
	 * @param int         $watermark_margin_right  Watermark margin from the right edge of the image.
	 * @param int         $watermark_margin_bottom Watermark margin from the bottom of the image.
	 * @param int|null    $jpg_quality             Set the jpeg quality. Default 75.
	 * @param int|null    $png_compression         Set png compression. Default 0.
	 * @param string|null $resampling_method       Set resampling method. Default IMG_BILINEAR_FIXED.
	 * @return array
	 */
	public static function resizeAndOutput(string $image_path,
										   array $sizes,
										   string $watermark_file = '',
										   bool $use_src_dir = true,
										   string $destination_dir = '',
										   bool $use_longer_dimension = true,
										   int $watermark_margin_right = 30,
										   int $watermark_margin_bottom = 30,
										   int $jpg_quality = null,
										   int $png_compression = null,
										   string $resampling_method = null
	): array
	{
		$image = new ResizeImages($image_path, $use_src_dir, $destination_dir);
		if ($jpg_quality !== null) {
			$image->setJpgQuality($jpg_quality);
		}
		if ($png_compression !== null) {
			$image->setPngCompression($png_compression);
		}
		if ($resampling_method !== null) {
			$image->setResamplingMethod($resampling_method);
		}
		$image->setOutputSizes($sizes, $use_longer_dimension);
		$image->setWatermark($watermark_file, $watermark_margin_right, $watermark_margin_bottom);
		$image->checkImages();
		return $image->outputImages();
	}

	/**
	 * Check directories and image. Create the resource and output final results.
	 * Returns an array filled with generated sizes and filenames along with any invalid results.
	 * @return array
	 */
	private function outputImages(): array
	{
		if (! \is_dir($this->upload_dir) || ! \is_writable($this->upload_dir)) {
			withMessage('Destination folder does not have the correct write permissions or is not a directory. Try again later and if the problem persists, contact Admin.', 'alert-danger');
			returnTo();
		}
		if (\in_array($this->raw_image_with_data['file'], $this->invalid)) {
			withMessage("File {$this->raw_image_with_data} is not a valid image file or is unreadable by the script. Check the file is doing ok and try again.", 'alert-danger');
			returnTo();
		}
		$resource = $this->createImageResource($this->raw_image_with_data['file'], $this->raw_image_with_data['type']);
		// Add a watermark if the watermark property contains a value
		if ($this->watermark !== '') {
			$this->addWatermark($this->raw_image_with_data, $resource);
		}
		// Delegate the generation of output to another method
		$this->generateOutput($this->raw_image_with_data, $resource);
		\imagedestroy($resource);

		return ['output' => $this->generated, 'invalid' => $this->invalid];
	}

	/**
	 * Set the JPG Quality.
	 * @param int $number
	 */
	private function setJpgQuality(int $number): void
	{
		if ($number < 0 || $number > 100) {
			\withMessage("Quality - {$number} - must be in the range of 0-100.", 'alert-danger');
			\returnTo();
		}
		$this->jpeg_quality = $number;
	}

	/**
	 * Set PNG Compression.
	 * @param int $number
	 */
	private function setPngCompression(int $number): void
	{
		if ($number < 0 || $number > 9) {
			\withMessage("Compression -{$number} - must be in the range of 0-9", 'alert-danger');
			\returnTo();
		}
		$this->png_compression = $number;
	}

	/**
	 * Set the Resampling method.
	 * @param string $value
	 */
	private function setResamplingMethod(string $value): void
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
	 * Set the desired output sizes.
	 * @param array $sizes
	 * @param bool  $use_longer_dimension
	 */
	private function setOutputSizes(array $sizes, bool $use_longer_dimension): void
	{
		foreach ($sizes as $size) {
			if (! \is_numeric($size) || $size <= 0) {
				\withMessage("Sizes must be declared as a positive number and greater than zero", 'alert-danger');
				\returnTo();
			}
			$this->output_sizes[] = (int) $size;
		}
		$this->use_longer_dimension = $use_longer_dimension;
		$this->calculateRatio();
	}

	/**
	 * Set the watermark file and margins.
	 * @param     $file
	 * @param int $margin_right
	 * @param int $margin_bottom
	 */
	private function setWatermark($file, $margin_right = 30, $margin_bottom = 30): void
	{
		if (! \file_exists($file) || ! \is_readable($file)) {
			\withMessage("Cannot access the watermark image - {$file}. Check the file location to verify it exists.", 'alert-danger');
			\returnTo();
		}
		$size = \getimagesize($file);
		if ($size === false && \mime_content_type($file) == 'image/webp') {
			$size['mime'] = 'image/webp';
		}

		if (! \in_array($size['mime'], $this->mime_types)) {
			\withMessage('Watermark must be one of the following types of images - ' . \implode(', ', $this->mime_types), 'alert-danger');
			\returnTo();
		}
		$this->watermark = $this->createImageResource($file, $size['mime']);
		if ($size['mime'] == 'image/webp') {
			$this->mark_width  = \imagesx($this->watermark);
			$this->mark_height = \imagesy($this->watermark);
		} else {
			$this->mark_width  = $size[0];
			$this->mark_height = $size[1];
		}
		if (\is_numeric($margin_right) && $margin_right > 0) {
			$this->margin_right = $margin_right;
		}
		if (\is_numeric($margin_bottom) && $margin_bottom > 0) {
			$this->margin_bottom = $margin_bottom;
		}
	}

	/**
	 * Generate the output files according to desired size array.
	 * Also watermarks the original file and replaces it if source is used as destination.
	 * @param $image
	 * @param $resource
	 */
	private function generateOutput($image, $resource): void
	{
//		$stored_sizes = $this->output_sizes;
		$name_parts = \pathinfo($image['file']);
		// Recalculate $output_sizes if the images height is greater than width
		if ($this->use_longer_dimension === true && \imagesy($resource) > \imagesx($resource)) {
			$this->recalculateSizes($resource);
		}
		foreach ($this->output_sizes as $output_size) {
			// Dont resize if current output is larger than original file
			if ($output_size >= $image['w']) {
				continue;
			}
			$scaled   = \imagescale($resource, (int) $output_size, -1, $this->resample);
			$filename = $name_parts['filename'] . '_' . $output_size . '.' . $name_parts['extension'];
			// Delegate file output to specialized method
			$this->outputFile($scaled, $image['type'], $filename);
		}
		$this->outputFile($resource, $image['type'], "{$name_parts['filename']}.{$name_parts['extension']}");
//		$this->output_sizes = $stored_sizes;
	}

	/**
	 * Check the current image. If exists, is readable and matches mime type.
	 */
	private function checkImages(): void
	{
		$this->raw_image_with_data['file'] = $this->raw_image;
		if (\file_exists($this->raw_image_with_data['file']) && \is_readable($this->raw_image_with_data['file'])) {
			$size = \getimagesize($this->raw_image_with_data['file']);

			if ($size === false && \mime_content_type($this->raw_image_with_data['file']) == 'image/webp') {
				$this->raw_image_with_data = $this->getWebpDetails($this->raw_image_with_data['file']);
			} elseif ($size[0] === 0 || ! \in_array($size['mime'], $this->mime_types)) {
				$this->invalid[] = $this->raw_image_with_data['file'];
			} else {
				if ($size['mime'] == 'image/jpeg') {
					$results                           = $this->checkJpgOrientation($this->raw_image_with_data['file'], $size);
					$this->raw_image_with_data['file'] = $results['file'];
					$size                              = $results['size'];
				}
				$this->raw_image_with_data['w']    = $size[0];
				$this->raw_image_with_data['h']    = $size[1];
				$this->raw_image_with_data['type'] = $size['mime'];
			}
		} else {
			$this->invalid[] = $this->raw_image_with_data['file'];
		}
	}

	/**
	 * Calculate the image ratio.
	 */
	private function calculateRatio(): void
	{
		$this->raw_image_with_data['ratios'] = [];
		if ($this->raw_image_with_data['h'] > $this->raw_image_with_data['w'] && $this->use_longer_dimension) {
			$divisor = $this->raw_image_with_data['h'];
		} else {
			$divisor = $this->raw_image_with_data['w'];
		}
		foreach ($this->output_sizes as $output_size) {
			$ratio                                 = $output_size / $divisor;
			$this->raw_image_with_data['ratios'][] = $ratio > 1 ? 1 : $ratio;
		}
	}

	/**
	 * Get webp format details.
	 * @param $image
	 * @return array
	 */
	private function getWebpDetails($image): array
	{
		$details         = [];
		$resource        = \imagecreatefromwebp($image);
		$details['file'] = $image;
		$details['w']    = \imagesx($resource);
		$details['h']    = \imagesy($resource);
		$details['type'] = 'image/webp';
		\imagedestroy($resource);
		return $details;
	}

	/**
	 * Check if the jpeg is in landscape or portrait orientation.
	 * @param $image
	 * @param $size
	 * @return array
	 */
	private function checkJpgOrientation($image, $size): array
	{
		$angle       = null;
		$output_file = $image;
		$exif        = \exif_read_data($image) ?: null;
		if (! \is_null($exif['Orientation'])) {
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
		if (! \is_null($angle)) {
			$original    = \imagecreatefromjpeg($image);
			$rotated     = \imagerotate($original, $angle, 0);
			$extension   = \pathinfo($image, PATHINFO_EXTENSION);
			$output_file = \str_replace(".$extension", '_rotated.jpg', $image);
			\imagejpeg($rotated, $output_file, 100);
			$size = \getimagesize($output_file);
			\imagedestroy($original);
			\imagedestroy($rotated);
		}
		return ['file' => $output_file, 'size' => $size];
	}

	/**
	 * Create an image resource from the provided file.
	 * @param $file
	 * @param $type
	 * @return false|resource
	 */
	private function createImageResource($file, $type)
	{
		switch ($type) {
			case 'image/jpeg':
				return \imagecreatefromjpeg($file);
				break;
			case 'image/png':
				return \imagecreatefrompng($file);
				break;
			case 'image/gif':
				return \imagecreatefromgif($file);
				break;
			case 'image/webp':
				return \imagecreatefromwebp($file);
				break;
		}
	}

	/**
	 * Add a watermark to the specified file.
	 * @param array $image
	 * @param       $resource
	 */
	private function addWatermark(array $image, $resource): void
	{
		$x = $image['w'] - $this->mark_width - $this->margin_bottom;
		$y = $image['h'] - $this->mark_height - $this->margin_right;
		\imagecopy($resource, $this->watermark, $x, $y, 0, 0, $this->mark_width, $this->mark_height);
	}

	/**
	 * Re-calculate the sizes for the image resource.
	 * @param $resource
	 */
	private function recalculateSizes($resource): void
	{
		// Get the width and height of the image resource
		$w = \imagesx($resource);
		$h = \imagesy($resource);
		foreach ($this->output_sizes as &$size) {
			// Multiply the size by the width divided by the height
			// Second argument of round() -1 rounds to the nearest multiple of 10
			$size = \round($size * $w / $h, -1);
		}
	}

	/**
	 * Write the new image to the filesystem and add to the generated array using size as the key.
	 * @param $scaled_final_image
	 * @param $type
	 * @param $name
	 */
	private function outputFile($scaled_final_image, $type, $name): void
	{
		$success     = false;
		$output_file = $this->upload_dir . DIRECTORY_SEPARATOR . $name;
		$db_output_file = Config::getUrlRoot() . $this->db_dir . DIRECTORY_SEPARATOR . $name;
		switch ($type) {
			case 'image/jpeg':
				$success = \imagejpeg($scaled_final_image, $output_file, $this->jpeg_quality);
				break;
			case 'image/png':
				$success = \imagepng($scaled_final_image, $output_file, $this->png_compression);
				break;
			case 'image/gif':
				$success = \imagegif($scaled_final_image, $output_file);
				break;
			case 'image/webp':
				$success = \imagewebp($scaled_final_image, $output_file);
				break;
		}
		\imagedestroy($scaled_final_image);
		if ($success) {
			$image_size                      = \getimagesize($output_file);
//			var_dump($this->upload_dir);
//			var_dump($output_file);
//			var_dump($name);
			$this->generated[$image_size[0]] = $db_output_file;
		}
	}

}