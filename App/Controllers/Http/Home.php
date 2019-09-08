<?php

namespace App\Controllers\Http;

defined('_KIKOPOLIS') or die('No direct script access!');

use Kikopolis\App\Helpers\Str;
use Kikopolis\App\Utility\Token;
use Kikopolis\Core\Factories\LoremIpsumFactory;
use App\Controllers\More;
use App\Controllers\Show;
use App\Controllers\Http\Posts;
use Kikopolis\App\Controllers\Controller;
use Kikopolis\App\Helpers\FileHelper;

class Home extends Controller
{
    public function __construct()
    {
        echo "<br>The Home Constructor<br>";
        echo "<h4>The Get Array</h4>";
        var_dump(!empty($_GET) ? $_GET : 'No get');
        echo "<br>";
        echo "<h4>The Post Array</h4>";
        var_dump(!empty($_POST) ? $_POST : 'No post');
        echo "<br>";
        echo FileHelper::getHumanFileSize('34000591231240');
    }

    public function index(Posts $posts, Show $show, More $more)
    {
        echo "<br><h1>Hi, cruel world of PHP</h1><br>";
        $string = Str::convertToSnakeCase('This to snake case');
        echo $string . '<br><br>';
        $string = Str::slug('This to slug case - Kiko@kiko');
        echo $string . '<br><br>';
        $string = Str::randomString(16);
        echo $string . ' - 16 chars<br><br>';
        $string = Str::convertToStudlyCase('This to studly case for shitz');
        echo $string . '<br><br>';
        $string = Str::convertToCamelCase('This to camel case for gigglz');
        echo $string . '<br><br>';
        $string = Str::limitWords('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sit amet volutpat consequat mauris nunc congue nisi vitae. At in tellus integer feugiat scelerisque. Tempor nec feugiat nisl pretium fusce id velit ut. Vitae purus faucibus ornare suspendisse. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Sed egestas egestas fringilla phasellus faucibus scelerisque. Nec ullamcorper sit amet risus nullam eget felis eget. Id volutpat lacus laoreet non curabitur. Ultricies lacus sed turpis tincidunt id. Mollis nunc sed id semper risus in hendrerit. Tristique magna sit amet purus gravida quis blandit. Non curabitur gravida arcu ac. A arcu cursus vitae congue. Enim eu turpis egestas pretium aenean. Mi tempus imperdiet nulla malesuada pellentesque elit eget gravida cum. Magna fringilla urna porttitor rhoncus dolor purus non. Tortor dignissim convallis aenean et tortor at risus viverra adipiscing. Pulvinar sapien et ligula ullamcorper malesuada. Amet massa vitae tortor condimentum lacinia quis vel. Consectetur purus ut faucibus pulvinar elementum. Praesent elementum facilisis leo vel fringilla est ullamcorper.');
        echo $string . '<br><br>';
        $token = new Token();
        $string = $token->getCsrfToken();
        echo $string . '<br><br>';
        var_dump($_SESSION);

        $lorem_ipsum = new LoremIpsumFactory();
        echo $lorem_ipsum->getLoremWords(100);

        if ($show) {
            var_dump($show->me());
        }
        if ($more) {
            var_dump($more->options());
        }
        print_r($posts->show());
    }
}