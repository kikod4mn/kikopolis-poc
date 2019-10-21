<?php

declare(strict_types=1);

$router->get('/', 'home.index', []);
$router->get('', 'home.index', []);
$router->get('index', 'home.index', []);
$router->get('home', 'home.index', []);
$router->get('faq', 'home.faq', []);
$router->get('about', 'home.about', []);
$router->get('contact', 'home.contact', []);

/**
 * Add your routes to this list.
 * It is recommended not to change any routes above this line unless you know what you are doing.
 */

$router->get('posts/show/{id}/{slug}', 'posts.show', []);
$router->get('posts/show/{slug}', 'posts.show', []);
$router->add(['post'],'form', 'form.display', []);