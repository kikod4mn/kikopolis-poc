<?php declare(strict_types=1);

$router->get('/', 'home.index', []);

/**
 * Add your routes to this list.
 * It is recommended not to change any routes above this line unless you know what you are doing.
 */

$router->get('posts/show/{id}/{slug}', 'posts.show', []);