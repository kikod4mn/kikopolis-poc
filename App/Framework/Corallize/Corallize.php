<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Corallize;

use Kikopolis\App\Helpers\Arr;
use Kikopolis\App\Utility\Regexp;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Corallize Theme Engine
 * @todo - Make two editing pages. one dynamic for all fields in the json and the second for the page content in the db directly.
 * @todo - Make backup with json array in the db as a longtext field. Keep 5-10 iterations as backup. Use date as name.
 * @todo - Add a single variable to the array
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Corallize
{
    private $regex = '/@tag::(?<type>\w*)\_(?<name>[\w\:]*)/';

    private static $tags = [];

    public static function setTags(array $tags) {
    	$tags = Arr::objToArr($tags);
		foreach ($tags as $tag) {
			self::$tags[$tag['tag']] = $tag;
    	}
	}

	public static function getTagsFromDb()
	{

	}

    private function coralHtmlTags(string $output): string
    {
        $matches = Regexp::findByRegex($this->regex, $output);
        foreach ($matches as $match) {
            $output = preg_replace('/' . $match[0] . '/', "<?php echo \Kikopolis\App\Framework\Corallize\Corallize::parse('{$match['type']}', '{$match['name']}'); ?>" , $output);
        }

        return $output;
    }

    public function process(string $output): string
    {
        $output = $this->coralHtmlTags($output);

        return $output;
    }

    public static function parse(string $type, string $name)
    {
        switch ($type) {
            case $type === 'meta':
                return "<meta name=\"{$name}\" content=\"" . self::$tags[$name]['value'] . "\">";
            case $type === 'paired':
                return "<{$name}>" . self::$tags[$name]['value'] . "</{$name}>";
			default:
				return "";
        }
    }
}