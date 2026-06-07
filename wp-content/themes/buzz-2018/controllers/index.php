<?php

use Firefly\Buzz\Timber\FireflyPost;

$context = Timber::get_context();
$context['post'] = new FireflyPost();

$context['posts'] = new Timber\PostQuery();
$context['pagination'] = Timber::get_pagination();

$templates = array( 'index.twig' );

Timber::render( $templates, $context );
