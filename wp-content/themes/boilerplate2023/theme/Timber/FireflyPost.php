<?php

namespace Firefly\Timber;

/*
 |--------------------------------------------------------------------------
 | Firefly Post
 |--------------------------------------------------------------------------
 |
 |  Firefly Post is an extension of the Timber Post class, which allows
 |  you to add additional functionality and call it right of the
 |  current post. For example {{ post.breadcrumb }}. A controller
 |  must use and set FireflyPost as the post object to access these methods
 |
 */

use Timber;
use Timber\Post as TimberPost;
use Firefly\Timber\PageMenu;
use Firefly\Timber\Breadcrumb;

class FireflyPost extends TimberPost {

	public function parent_menu() {
        return PageMenu::render( $this );
	}

    public function breadcrumb($args = []) {
        return Breadcrumb::render($args);
    }

    public function get_field( $field_name ) {
	    $value = carbon_get_post_meta($this->id, 'ff_' . $field_name);
	    $value = $this->convert($value, __CLASS__);
	    return $value;
	}

	public function should_show_thumbnail()
	{
		if( ! has_post_thumbnail( $this->id ) ) return false;

		if( is_search() || is_home() ) return false;

		return true;
	}

    public function thumbnail_id() {
		if( ! $this->should_show_thumbnail() ) {
			return false;
		}

        if( is_preview() ) {
			return $_GET['_thumbnail_id'];
		} else {
			return has_post_thumbnail( $this->id ) ? get_post_thumbnail_id( $this->id ) : false;
		}
	}

	public function root_parent_post()
	{
		if ($this->post_parent)	{
			$ancestors = get_post_ancestors($this->ID);
			return $ancestors[count($ancestors) - 1];
		} else {
			return $this->ID;
		}
	}

	/**
	 * Get Social Link
	 * Get a single social link from the theme options
	 *
	 * @param [string] $type
	 * @return array
	 */
	public function get_social_option( $type ) {
		// Get all the social links from the theme options
		$links = carbon_get_theme_option( 'ff_social_links' );
		// cast the type to lowercase to assist with comparison
		$type = strtolower($type);

		// Loop the social links and return the link that matches the type
		foreach( $links as $link ) {
			if( strpos(strtolower($link['url']), $type) !== false ) {
				return $link;
			}
		}
		// If we were unable to find a matching link, return an empty array
		return [];
	}
}