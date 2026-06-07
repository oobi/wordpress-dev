<?php

namespace Firefly\Setup;

use Timber\Timber;

class Widgets
{

    public function __construct()
    {
        add_action('widgets_init', array($this, 'widgets_init'));
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
    }

    public function widgets_init()
    {
		// header widget area
		register_sidebar( array(
            'name'          => ff__( 'Header' ),
            'id'            => 'buzz-widget-header',
            'description'   => ff__( 'Add widgets here to appear in the header (above text/image)' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		// before featured articles widget area
		register_sidebar( array(
            'name'          => ff__( 'Before Featured Article(s)' ),
            'id'            => 'buzz-widget-before-featured',
            'description'   => ff__( 'Add widgets here to appear in the area before the featured article(s)' ),
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		// before index articles widget area
		register_sidebar( array(
            'name'          => ff__( 'Between Featured/Index Articles' ),
            'id'            => 'buzz-widget-between-featured-index',
            'description'   => ff__( 'Add widgets here to appear in the area between the featured and index articles' ),
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		// after index articles widget area
		register_sidebar( array(
            'name'          => ff__( 'After Index Articles' ),
            'id'            => 'buzz-widget-after-index',
            'description'   => ff__( 'Add widgets here to appear in the area after the index articles' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		// footer widget area
        register_sidebar( array(
            'name'          => ff__( 'Footer' ),
            'id'            => 'buzz-widget-footer',
            'description'   => ff__( 'Add widgets here to appear in footer. Add multiple widgets to create columns.' ),
            'before_widget' => '<section id="%1$s" class="widget col-12 col-md %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		// footer widget area
        register_sidebar( array(
            'name'          => ff__( 'Sidebar' ),
            'id'            => 'buzz-widget-sidebar',
            'description'   => ff__( 'Add widgets here to appear in article page sidebar.' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
		) );

		if( class_exists( 'Buzz_Addon_Email_View' ) ) {
			// before featured articles widget area
			register_sidebar( array(
				'name'          => ff__( '[Email View] Before Featured Article(s)' ),
				'id'            => 'buzz-widget-email-before-featured',
				'description'   => ff__( 'Add widgets here to appear in the email view before the featured articles.
									Images used in widgets should be max 640 pixels wide.' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );

			// before index articles widget area
			register_sidebar( array(
				'name'          => ff__( '[Email View] Between Featured/Index Articles' ),
				'id'            => 'buzz-widget-email-between-featured-index',
				'description'   => ff__( 'Add widgets here to appear in the email view between the featured and index articles.
									Images used in widgets MUST be max 640 pixels wide.' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );

			// after index articles widget area
			register_sidebar( array(
				'name'          => ff__( '[Email View] After Index Articles' ),
				'id'            => 'buzz-widget-email-after-index',
				'description'   => ff__( 'Add widgets here to appear in the email view after the index articles.
									Images used in widgets MUST be max 640 pixels wide.' ),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );
		}
    }

    public function add_to_context( $context )
    {
		// add all widgets to context in an array so they can be looped
        $context['widgets']['header'] = Timber::get_widgets('buzz-widget-header');
        $context['widgets']['before_featured'] = Timber::get_widgets('buzz-widget-before-featured');
        $context['widgets']['between_featured_index'] = Timber::get_widgets('buzz-widget-between-featured-index');
        $context['widgets']['after_index'] = Timber::get_widgets('buzz-widget-after-index');
        $context['widgets']['sidebar'] = Timber::get_widgets('buzz-widget-sidebar');
        $context['widgets']['footer'] = Timber::get_widgets('buzz-widget-footer');

		// if email view is active
		if( class_exists( 'Buzz_Addon_Email_View' ) ) {
			$context['widgets']['email']['before_featured'] = Timber::get_widgets('buzz-widget-email-before-featured');
			$context['widgets']['email']['between_featured_index'] = Timber::get_widgets('buzz-widget-email-between-featured-index');
			$context['widgets']['email']['after_index'] = Timber::get_widgets('buzz-widget-email-after-index');
		}

        return $context;
    }
}
