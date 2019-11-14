<?php

declare(strict_types=1);

$router->get('/', 'home.index', []);
$router->get('index', 'home.index', []);
$router->get('home', 'home.index', []);

/**
 * Administrative routes
 */
$router->get('admin', 'admin.index', ['namespace' => 'Admin']);
//$router->resource('theme', 'themes', ['namespace' => 'Admin'], ['show']);
//$router->resource('tag', 'tags', ['namespace' => 'Admin'], ['show']);
$router->resource('content', 'contents', ['namespace' => 'Admin'], ['show']);

/**
 * Add your routes to this list.
 * It is recommended not to change any routes above this line unless you know what you are doing.
 */
$router->get('images/{slug}', 'gallery.single');
$router->get('gallery/{category}/show', 'gallery.show', []);
$router->get('posts/show/{id}/{slug}', 'posts.show', []);
$router->get('posts/show/{slug}', 'posts.show', []);

//var_dump($router->getRoutes());
//die;