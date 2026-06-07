<?php

namespace Firefly\Setup;

use Timber\Timber;

class Widgets
{
    public function __construct()
    {
        add_action('widgets_init', [$this, 'widgets_init']);
        add_filter('timber_context', [$this, 'add_to_context']);
    }

    public function widgets_init()
    {
        // Footer Column One
        register_sidebar([
            'name'          => __('Footer Column One', 'firefly'),
            'id'            => 'widget-footer-column-one',
            'description'   => __('Footer Widget', 'firefly'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
        // Footer Column One
        register_sidebar([
            'name'          => __('Footer Column Two', 'firefly'),
            'id'            => 'widget-footer-column-two',
            'description'   => __('Footer Widget', 'firefly'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
        // Footer Column One
        register_sidebar([
            'name'          => __('Footer Column Three', 'firefly'),
            'id'            => 'widget-footer-column-three',
            'description'   => __('Footer Widget', 'firefly'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
        // Footer Column One
        register_sidebar([
            'name'          => __('Footer Column Four', 'firefly'),
            'id'            => 'widget-footer-column-four',
            'description'   => __('Footer Widget', 'firefly'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ]);
    }

    public function add_to_context( $context )
    {
        $context['widget_footer_column_one'] = Timber::get_widgets('widget-footer-column-one');
        $context['widget_footer_column_two'] = Timber::get_widgets('widget-footer-column-two');
        $context['widget_footer_column_three'] = Timber::get_widgets('widget-footer-column-three');
        $context['widget_footer_column_four'] = Timber::get_widgets('widget-footer-column-four');
        
        return $context;
    }
}
