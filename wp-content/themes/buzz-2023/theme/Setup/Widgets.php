<?php

namespace Firefly\Setup;

use Timber\Timber;

class Widgets
{

	public function __construct()
	{
		add_action('widgets_init', array($this, 'widgets_init'));
		add_filter('timber_context', array($this, 'add_to_context'));
	}

	public function widgets_init()
	{
		if (class_exists('Buzz_Addon_Email_View')) {
			// before featured articles widget area
			register_sidebar(array(
				'name'          => ff__('[Email View] Before Featured Article(s)'),
				'id'            => 'buzz-widget-email-before-featured',
				'description'   => ff__('Add widgets here to appear in the email view before the featured articles.
									Images used in widgets should be max 640 pixels wide.'),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			));

			// before index articles widget area
			register_sidebar(array(
				'name'          => ff__('[Email View] Between Featured/Index Articles'),
				'id'            => 'buzz-widget-email-between-featured-index',
				'description'   => ff__('Add widgets here to appear in the email view between the featured and index articles.
									Images used in widgets MUST be max 640 pixels wide.'),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			));

			// after index articles widget area
			register_sidebar(array(
				'name'          => ff__('[Email View] After Index Articles'),
				'id'            => 'buzz-widget-email-after-index',
				'description'   => ff__('Add widgets here to appear in the email view after the index articles.
									Images used in widgets MUST be max 640 pixels wide.'),
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			));
		}
	}

	public function add_to_context($context)
	{
		// if email view is active
		if (class_exists('Buzz_Addon_Email_View')) {
			$context['widgets']['email']['before_featured'] = Timber::get_widgets('buzz-widget-email-before-featured');
			$context['widgets']['email']['between_featured_index'] = Timber::get_widgets('buzz-widget-email-between-featured-index');
			$context['widgets']['email']['after_index'] = Timber::get_widgets('buzz-widget-email-after-index');
		}

		return $context;
	}
}
