<?php

use Firefly\Buzz\Timber\FireflyPost;

$context = Timber::get_context();
$post = new FireflyPost();
$context['post'] = $post;
$context['parent'] = new TimberPost($post->post_parent);

Timber::render( 'page.twig' , $context );