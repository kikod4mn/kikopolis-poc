<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Cardinal;

use App\Models\Framework\Content;
use Kikopolis\App\Utility\Regexp;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Cardinal
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Cardinal
{
    private $regex = '/@cms::(?<var>\w*)/';

    public function process(string $output): string
    {
        $output = $this->cmsTags($output);

        return $output;
    }

    /**
     * Retrieve the page content from the database.
     * Incoming $file_name must be a valid page route eg 'home.index'.
     * @param string $file_name
     * @return array
     * @throws \Exception
     */
    public static function content(string $file_name)
    {

    }

    private function cmsTags(string $output): string
    {
        $matches = Regexp::findByRegex($this->regex, $output);
        foreach ($matches as $match) {
            $output = preg_replace('/' . $match[0] . '/', "<?php echo \Kikopolis\App\Framework\Aurora\Aurora::k_echo(\${$match['var']}, 'escape'); ?>", $output);
        }

        return $output;
    }
}