<?php

declare(strict_types=1);

namespace Kikopolis\App\Framework\Corallize;

use Kikopolis\App\Utility\Regexp;

defined('_KIKOPOLIS') or die('No direct script access!');

/**
 * Corallize Theme Engine
 * Part of the Kikopolis MVC Framework.
 * @author Kristo Leas <admin@kikopolis.com>
 * @version 0.0.0.1000
 * PHP Version 7.3.5
 */
class Corallize
{
    private $regex = '/@coral::(?<type>\w*)\_(?<name>[\w\:]*)/';

    public static $tags = [
        'meta' => [
            'description' => 'The site meta description content',
            'copyright' => 'The copyright info of the website',
            'keywords' => 'the, keywords, for, the, website',
            'twitter:card' => 'Twitter card',
            'twitter:site' => '@kikopolis',
            'twitter:title' => 'Showcase of Kikopolis',
            'twitter:description' => 'Most awesome MVC Framework and CMS around',
            'twitter:image' => 'https://farm6.staticflickr.com/5510/14338202952_93595258ff_z.jpg',
            'og:title' => 'Facebook share',
            'og:description' => 'Most awesome MVC Framework and CMS around',
            'og:image' => 'http://euro-travel-example.com/thumbnail.jpg',
            'og:url' => 'http://euro-travel-example.com/index.htm',
        ],
        'pairedTag' => [
            'title' => 'The awesome title of the page',
        ],
        'theme' => [
            'logoColor' => 'black',
            'bgPrimary' => 'pink',
            'bgSecondary' => 'red',
            'linkColor' => 'gray'
        ]
    ];

    public function theme()
    {

    }

    public function content()
    {

    }

    public static function parse(string $type, string $name)
    {
        switch ($type) {
            case $type === 'meta':
                return "<meta name=\"{$name}\" content=" . self::$tags['meta'][$name] . ">";
            case $type === 'pairedTag':
                return "<{$name}>" . self::$tags['pairedTag'][$name] . "</{$name}>";
            case $type === 'theme':
                return self::$tags['theme'][$name];
        }
    }

    public static function process(string $theme_content): string
    {
        $coral = new Corallize();
        $matches = Regexp::findByRegex($coral->regex, $theme_content);
        var_dump($matches);
        foreach ($matches as $match) {
            $theme_content = preg_replace('/' . $match[0] . '/', "<?php echo \Kikopolis\App\Framework\Corallize\Corallize::parse('{$match['type']}', '{$match['name']}'); ?>" , $theme_content);
        }

        return $theme_content;
    }
}