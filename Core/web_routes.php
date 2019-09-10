<?php

/**
 * Add your routes to this file.
 */

$router->get('/', 'home.index', []);
$router->get('home/index', 'home.index', []);
$router->get('home/about', 'home.about', []);
$router->get('home/faq', 'home.faq', []);
$router->get('posts/view/{slug:\d+}', 'posts.view', []);
$router->get('posts/show', 'posts.show.show.me.more.options', []);