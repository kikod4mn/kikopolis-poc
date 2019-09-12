<?php

/**
 * Add your routes to this file.
 */

$router->get('/', 'home.index', []);
$router->get('posts/show/{id:\d+}/{slug:[a-z0-9-]+}', 'posts.show', []);
// $router->get('home/index', 'home.index', []);
// $router->get('home/about', 'home.about', []);
// $router->get('home/faq', 'home.faq', []);
// $router->get('posts/view/{slug:\d+}', 'posts.view', []);
// $router->get('posts/show', 'posts.show', []);