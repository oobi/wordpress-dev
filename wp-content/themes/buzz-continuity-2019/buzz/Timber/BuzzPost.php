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
use Firefly\Buzz\Setup\Newsletter;
use Firefly\Buzz\Setup\Articles;

class BuzzPost extends TimberPost {

	// Return the article parent newsletter if it exists
	public function newsletter() {
        if( isset( $this->ff_parent_id ) ) {
			return Timber::get_post($this->ff_parent_id);
		}
		return false;
	}

	// Is the article featured?
	public function is_featured() {
        if( isset( $this->ff_featured_article ) ) {
			return 'featured';
		}
		return false;
	}

	public function is_in_email() {
        return isset( $this->ff_featured_email );
	}

	/**
	 * Get the classes relevant to the BuzzPost
	 */
	public function classes() {
		// check if taxonomies add on is enabled
		if( !class_exists( 'Buzz_Addon_Taxonomies' ) ) { return false; }

		// get the categories and tags on the post
		$cat = \Buzz_Addon_Taxonomies::$category;
		$tag = \Buzz_Addon_Taxonomies::$tag;
		$terms = $this->terms([$cat, $tag]);

		// build classes for output
		$output = ' '; // preceding whitespace to avoid class collisions
		if( !empty( $terms ) ) {
			foreach( $terms as $t ) {
				$slug 		= $t->slug;
				$taxonomy 	= $t->taxonomy;
				$output 	.= "buzz-$taxonomy-$slug "; // ensure space on end of string
			}
		}

		return $output;
	}

	/**
	 * Get a single category or tag and output as a class
	 * @param 	{Object}	$tax 			A term
	 * @param 	{Boolean}	$add_generic 	Add a generic class before tax-specific classes
	 */
	public function tax_class( $term, $add_generic=false ) {
		// check if taxonomies add on is enabled
		if( !class_exists( 'Buzz_Addon_Taxonomies' ) ) { return false; }

		// build class and output
		$slug 		= $term->slug;
		$taxonomy 	= $term->taxonomy;
		$prefix		= $add_generic ? " buzz-$taxonomy " : ' '; // ensure space on end of prefix

		return $prefix . "buzz-$taxonomy-$slug";
	}

	// private function build_taxonomy_class(  $slug,  ) {
	// 	return
	// }

}