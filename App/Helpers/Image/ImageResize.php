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
    protected $webp_supported = true;
    protected $use_imagescale = true;
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

}