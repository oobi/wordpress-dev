<?php

namespace Firefly\Buzz\Timber;

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
use Firefly\Buzz\Timber\PageMenu;

class FireflyPost extends TimberPost {

	public function parent_menu() {
        return PageMenu::render( $this );
	}

	public function should_show_thumbnail() {
		if( ! has_post_thumbnail( $this->id ) ) return false;

		if( is_search() ) return false;

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

}