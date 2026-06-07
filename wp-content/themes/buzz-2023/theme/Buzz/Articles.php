<?php

namespace Firefly\Buzz;

use Timber\Timber;
use Firefly\Timber\BuzzPost;
use Firefly\Customizer\Customizer;

class Articles {

    protected $newsletter;
	public $articles;

    public function __construct( $newsletter = null ) {
		$this->newsletter 		= $newsletter;
		$this->articles 		= [];
    }

    /**
     * Get all articles belonging to the newsletter
     *
     * @return Articles
     */
    public function get() {

		// if an entire newsletter object is passed, get the ID
		if( is_object( $this->newsletter ) ) {
			$ID = $this->newsletter->id;
		}
		// if a number is passed, assume it is a newsletter ID
		else if( is_numeric( $this->newsletter ) ) {
			$ID = $this->newsletter;
		}
		// otherwise return a blank array
		else {
			return [];
		}

        $args = array(
            'posts_per_page'=> -1,
            'post_type' 	=> 'article',
            'post_status'	=> current_user_can('edit_others_posts') ? 'any' : 'publish',
            'orderby'		=> 'menu_order',
            'order'			=> 'ASC',
            'meta_query'    => array(
                array(
                    'key' 		=> 'ff_parent_id',
                    'value' 	=> $ID,
                    'compare'	=> '='
                )
            )
        );

        $this->articles = Timber::get_posts( $args, 'Firefly\Timber\BuzzPost' ); // get as BuzzPosts

        return $this;
    }

    /**
     * Filter the articles by a key and value
     *
     * @return Articles
     */
    public function filter( $key = 'ff_featured_email', $value = 'featured' ) {

        $this->articles = array_filter($this->articles, function($article) use ($key, $value) {
            return $article->$key === $value;
        });

        return $this;
	}

	/**
	 * Pluck and retrieve a categorised list of articles, if the taxonomy plugin is active
	 */
	public static function categorize( &$articles, $include_uncategorized=true ) {
		$result = [];

		if( class_exists('Buzz_Addon_Taxonomies')) {
			$categories = \Buzz_Addon_Taxonomies::get_categories();
			foreach($categories as $term) {
				$items = Articles::pluck_category($articles, $term->term_id);
				$result[$term->slug] = array(
					'name'	=> $term->name,
					'slug'	=> $term->slug,
					'posts' => $items
				);
			}
		}

		if( $include_uncategorized ) {
			$result['uncategorized']['name'] = Customizer::get_theme_mod( 'buzz_index_page_title' );
			$result['uncategorized']['slug'] = 'uncategorized';
			$result['uncategorized']['posts'] = $articles;
			// everything was plucked
			$articles = [];
		}

		// uncategorized should be first
		if( count( $result ) ) {
			$last = array_pop( $result );
			array_unshift( $result, $last);
		}

		return $result;
	}

	/**
	 * Pluck articles one at a time from a given set
	 *
	 * @param 	{Array} 	$items 		Array of post objects to pull articles from (passed by reference)
	 */
	public static function pluck( Array &$items, $qty=0, $flags=array(), $fill=true ) {
		$result = array();
		$all = false;
		$items = array_values($items); // reindex (non-sequential keys causes issues here)

		// no quantity? Return empty array
		if( !$qty ) {
			return $result;
		}
		// quantity less than zero? Return all matching items
		else if( $qty < 0 ) {
			$qty = PHP_INT_MAX;
			$all = true;
		}

		// fill to rounded number?
		$roundto = is_numeric($fill) ? abs(intval($fill)) : false;

		// work out maximum available return quantity
		$num_items = min( [$qty, count($items) ]);
		$index = 0;

		// loop to retrieve matching items
		while( count($result) < $num_items ) {
			$item = $items[$index];

			if( self::match_flags( $item, $flags ) ) {
				$result[] = $item;
				array_splice( $items, $index, 1);
			}
			// don't increment if we removed an element
			else {
				$index++;
			}


			// if we've gone thru once and not filled our quota
			// either break out (no fill)
			// or restart the loop with no conditions (fill)
			if( $index >= count($items) ) {
				if( $fill === true ) {
					$index = 0;
					$flags = null;
				} else {
					break;
				}
			}

		}

		// post-process rounding
		// if we are selecting ALL matching items but we want to fill to
		// a multiple as defined by $roundto then fetch the remaining items
		if($all && $roundto) {
			$remain = ($roundto - (count($result) % $roundto)) % $roundto;

			if( $remain > 0) {
				$additional = self::pluck($items, $remain, null, false);
				$result = array_merge( $result, $additional );
			}
		}
		return $result;
	}

	/**
	 * Pluck all articles with matching taxonomy term
	 */
	public static function pluck_taxonomy( &$items, $taxonomy, $term_id='' ) {
		$terms = \Buzz_Addon_Taxonomies::get_terms( $taxonomy );
		$result = array();

		if( ! $term_id  ) return $result;

		$index = 0;
		while( $index < count($items) ) {
			$item = $items[$index];
			// if item has term, pluck it
			if( $item->has_term( $term_id, $taxonomy ) ) {
				$result[] = $item;
				array_splice( $items, $index, 1);
			 }
			 // don't increment if we removed an element
			 else {
				$index++;
			 }
		}

		return $result;
	}

	/**
	 * Pluck all articles with matching category
	 */
	public static function pluck_category( &$items, $term_id='') {
		return self::pluck_taxonomy( $items, \Buzz_Addon_Taxonomies::$category, $term_id );
	}

	/**
	 * Match article flags
	 */
	private static function match_flags( $item, $flags=null ) {
		if( ! $flags ) {
			return true;
		}

		$result = true;
		foreach( $flags as $flag ) {
			$item_value = isset($item->$flag) ? !! $item->$flag : false;
			$result &= ($item_value == $flag);
		}

		return $result;
	}

}
