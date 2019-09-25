<?php

namespace App\Http\Controllers;

defined('_KIKOPOLIS') or die('No direct script access!');

use App\Models\Post;
use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;
use Kikopolis\App\Utility\Token;
use Kikopolis\Core\Factories\LoremIpsumFactory;
use Kikopolis\App\Framework\Controllers\Controller;
use Kikopolis\App\Helpers\FileHelper;
use Kikopolis\Core\Http\Request;
use Kikopolis\Core\Aurora\View;

class Home extends Controller
{
    public function __construct()
    {
        // echo "<br>The Home Constructor<br>";
        // echo "<h4>The Get Array</h4>";
        // var_dump(!empty($_GET) ? $_GET : 'No get');
        // echo "<br>";
        // echo "<h4>The Post Array</h4>";
        // var_dump(!empty($_POST) ? $_POST : 'No post');
        // echo "<br>";
        // echo FileHelper::getHumanFileSize('34000591231240');
    }

    public function index(Post $post)
    {
        $lorem_ipsum = new LoremIpsumFactory();

        $american_league = [
            'Rays', 'Yankees', 'Blue Jays', 'Orioles', 'Red Sox', 'Royals', 'Twins',
            'Tigers', 'Indians', 'White Sox', 'Astros', 'Rangers', 'Angels',
            'Mariners', 'Athletics'
        ];

        return View::render('home.index', [
            'page_title' => '<i>The dynamic title of the page in italic</i>',
            'heading_title' => '<i>The title of lorems<script>alert(\'alert\');</script></i>',
            'content' => '<a href="#">The link</a><br>' . $lorem_ipsum->getLoremWords(250),
            'no_escape' => '
            <div class="container" id="blink"><h1 class="text-center">No escaping here!!! JavaScript injects supreme!!!!!</h1>
            <script>window.addEventListener("load", function() {
                var f = document.getElementById("blink");
                setInterval(function() {
                    f.style.color = (f.style.color == "red" ? "" : "red");
                }, 200);
            
            }, false);</script></div>',
            'users' => [
                'user1' => [
                    'id' => '1',
                    'name' => 'John',
                    'email' => 'john@doe.com'
                ],

                'user2' => [
                    'id' => '1',
                    'name' => 'Jane',
                    'email' => 'jane@doe.com'
                ]
            ],
            'posts' => $post->get(),
            'teams' => $american_league
        ]);
        // echo "<br><h1>Hi, cruel world of PHP</h1><br>";
        // $string = Str::convertToSnakeCase('This to snake case');
        // echo $string . '<br><br>';
        // $string = Str::slug('This to slug case - Kiko@kiko');
        // echo $string . '<br><br>';
        // $string = Str::randomString(16);
        // echo $string . ' - 16 chars<br><br>';
        // $string = Str::convertToStudlyCase('This to studly case for shitz');
        // echo $string . '<br><br>';
        // $string = Str::convertToCamelCase('This to camel case for gigglz');
        // echo $string . '<br><br>';
        // $string = Str::limitWords('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sit amet volutpat consequat mauris nunc congue nisi vitae. At in tellus integer feugiat scelerisque. Tempor nec feugiat nisl pretium fusce id velit ut. Vitae purus faucibus ornare suspendisse. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Sed egestas egestas fringilla phasellus faucibus scelerisque. Nec ullamcorper sit amet risus nullam eget felis eget. Id volutpat lacus laoreet non curabitur. Ultricies lacus sed turpis tincidunt id. Mollis nunc sed id semper risus in hendrerit. Tristique magna sit amet purus gravida quis blandit. Non curabitur gravida arcu ac. A arcu cursus vitae congue. Enim eu turpis egestas pretium aenean. Mi tempus imperdiet nulla malesuada pellentesque elit eget gravida cum. Magna fringilla urna porttitor rhoncus dolor purus non. Tortor dignissim convallis aenean et tortor at risus viverra adipiscing. Pulvinar sapien et ligula ullamcorper malesuada. Amet massa vitae tortor condimentum lacinia quis vel. Consectetur purus ut faucibus pulvinar elementum. Praesent elementum facilisis leo vel fringilla est ullamcorper.', 100);
        // echo $string . '<br><br>';
        // $token = new Token();
        // $string = $token->getCsrfToken();
        // echo $string . '<br><br>';
        // var_dump($_SESSION);
        // $request = Request::createFromGlobals();
        // var_dump($request);

        // echo $lorem_ipsum->getLoremWords(100);
    }
}
