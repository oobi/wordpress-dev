<?php

/**
 * The public-facing functions used by the theme
 *
 * @link       http://www.fireflyinteractive.net
 * @since      3.0.0
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/public
 */

if ( ! function_exists( 'ff_get_newsletter_option' ) ) :
/**
 * TODO: tag
 */
function ff_get_newsletter_option( $option, $default = NULL ) {

	$plugin = FF_Newsletter::get_instance();
	return $plugin->get_option( $option, $default );

}
endif;

/*****************************************************************************
 * ARTICLES
 *****************************************************************************/

// retrieve articles based on custom fields
if ( ! function_exists( 'ff_get_article_query' ) ) :
function ff_get_article_query($options=array()) {
	return FF_Newsletter_Common::get_article_query($options);
}
endif;

// retrieve article ARRAY based on custom fields
if ( ! function_exists( 'ff_get_articles' ) ) :
function ff_get_articles($options=array()) {
	return FF_Newsletter_Common::get_articles($options);
}
endif;

// retrieve adjacent articles to the current one as per the query
if ( ! function_exists( 'ff_get_adjacent_articles' ) ) :
function ff_get_adjacent_articles($current_article=null) {
	global $post;
	if(!$current_article) $current_article = $post;

	// defaults
    $isFound = 0;
	$result = array(
		'previous' 	=> null,
		'current'	=> $current_article,
		'next'		=> null
	);

	// if we have a article then find its siblings
	if($current_article) {

		// get all newsletters
		$parent_id 	= get_post_meta( $current_article->ID, 'ff_parent_id', TRUE );
		$articles 	= FF_Newsletter_Common::get_articles( array('parent-id' => $parent_id) );
		$previous 	= -1;
		$next 	  	= 0;
		$count 	  	= 0;

		// iterate over newsletters to find next/previous indicies
		foreach($articles as $article) {
			if( $article->ID == $current_article->ID ){
				$previous = $count-1;
				$next 	  = $count+1;
				break;
			}
			$count++;
		}

		if($previous >= 0) 			 $result['previous'] = $articles[$previous];
		if($next < count($articles)) $result['next'] 	 = $articles[$next];
	}

	return $result;
}
endif;

// get an array of article links
if ( ! function_exists( 'ff_get_article_list' ) ) :
function ff_get_article_list($articles) {
	global $post;

	// take article post objects and create array of data we need
	$output = array();
	foreach($articles as $article) {
		$post_meta = get_post_meta($article->ID, 'ff_featured_article', TRUE);

		$item = array(
			'id'			=> $article->ID,
			'featured' 		=> !empty($post_meta),
			'permalink'		=> get_permalink($article->ID),
			'title'			=> $article->post_title
		);
		array_push($output, $item);

	}
	return $output;
}
endif;

// get an array of article links grouped by category
if ( ! function_exists( 'ff_get_article_category_list' ) ) :
function ff_get_article_category_list($newsletter, $options=array(), $taxonomy='article_category') {
	if(!$newsletter) $newsletter = ff_get_latest_newsletter();
	if(!$newsletter) return '';

	// merge options with defaults
	$options = array_merge(array(
		'uncategorized_first'	=> TRUE,
		'use_categories'		=> taxonomy_exists($taxonomy)
	), $options );

	// should we categorise our article list?
	$taxonomies_active = $options['use_categories'];

	// setup output array
	$output = array(
		'taxonomies_active' 	=> $taxonomies_active,
		'articles' 				=> array(), // filled with articles below
	);

	// if the taxonomy does not exist then deliver a plain list
	if(!$taxonomies_active) {
		// get all articles in newsletter and add to output array
		$articles = ff_get_articles(array(
			'parent-id' => $newsletter->ID,
		));
		$output['articles'] = ff_get_article_list($articles);
	}
	// otherwise generate a categorised list
	else {
		// get terms
		$terms 		= ff_get_terms_by_issue($taxonomy, $newsletter->ID);
		$term_ids   = array();

		// this array will accumulate article IDs as they are rendered
		// we do not want to show articles under multiple categories
		$exclude = array();

		// loop over available categories
		foreach($terms as $term) {
			// get articles by category
			$term_ids[] = $term->term_id;
			$articles = ff_get_articles(array(
				'post__not_in' 	=> $exclude,
				'parent-id' 	=> $newsletter->ID,
				'tax_query' 	=> array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => array($term->term_id)
					)
				)
			));

			// if category contains articles, push onto output array
			if($articles && !empty($articles)) {
				$cat_data = array(
					'slug'		=> $term->slug,
					'name'		=> $term->name,
					'description'=> $term->description,
					'articles'	=> ff_get_article_list($articles)
				);

				array_push( $output['articles'], $cat_data );
			}

			// keep track of articles to exclude from 'uncategorized' category later
			foreach($articles as $article) {
				$exclude[] = $article->ID;
			}
		}

		// articles which have no category
		$articles = ff_get_articles(array(
			'post__not_in' => $exclude,
			'parent-id' => $newsletter->ID,
			'tax_query' => array(
				array(
					'taxonomy' => 'article_category',
					'field'    => 'term_id',
					'terms'    => $term_ids,
					'operator' => 'NOT IN'
				)
			)
		));

		// create 'uncategorized' category
		$uncategorized_data = array(
			'slug'		=> FALSE,
			'name'		=> FALSE,
			'description'=> FALSE,
			'articles'	=> ff_get_article_list($articles)
		);

		if($options['uncategorized_first']) {
			// uncategorised articles first
			if( $uncategorized_data['articles'] ) {
				array_unshift( $output['articles'], $uncategorized_data );
			}
		} else {
			// uncategorised articles last
			if( $uncategorized_data['articles'] ) {
				array_push( $output['articles'], $uncategorized_data );
			}
		}

		// sort the categories by description
		// TODO: better way of sorting categories that is less hacky.
		uasort( $output['articles'], function( $a, $b ) {
			return intval( $a['description'] ) - intval( $b['description'] );
		});

	} // end else

	return $output;
}
endif;

// Check if the current post matches an article ID
if ( ! function_exists( 'ff_is_current_article' ) ) :
function ff_is_current_article( $post, $article_id ) {
	if( $post && $post->ID == $article_id ) {
		return TRUE;
	}
	return FALSE;
}
endif;

// Format the WP Query post object to be displayed
if ( ! function_exists( 'ff_format_article_array' ) ) :
function ff_format_article_array( $all_articles=NULL, $num_featured=1, $categorize=false ) {

	// return if no post object passed
	if( !$all_articles ) {
		return NULL;
	}

	// get articles from post object
	$articles = $all_articles->posts;

	// create loop variables
	$formatted_array = array( 'featured' => array(), 'article' => array() );
	$count_featured = 0;

	// loop through articles
	foreach( $articles as $article ) {

		// get article variables
		$id 			= $article->ID;
		$title 			= $article->post_title;
		$permalink 		= get_the_permalink( $article->ID );
		$has_thumbnail	= has_post_thumbnail( $article->ID );
		$is_featured	= get_post_meta( $article->ID, 'ff_featured_article' ) ? true : false;

		// if we are categorizing the articles and the taxonomy exists (ie. plugin is active), get the categories
		$has_categories = false;
		if( $categorize && taxonomy_exists( 'article_category' ) ) {
			$categories 	= get_the_terms( $article, 'article_category' );
			$has_categories = !!$categories;
		}
		// if taxonomy doesn't exist set to not categorize
		else {
			$categorize = false;
		}

		// assign variables to data array
		$data = array();
		$data['id'] 		= $id;
		$data['title'] 		= $title;
		$data['permalink'] 	= $permalink;
		$data['has_thumb']	= $has_thumbnail;

		// if article is also featured, change article type
		$article_type = 'article'; // reset article type
		if( $is_featured ) {
			// if within max number of features, change type
			if( ++$count_featured <= $num_featured ) {
				$article_type = 'featured';
			}
		}

		// if categorization and not featured
		if( $categorize && $article_type !== 'featured' ) {

			// if article has a category
			if( $has_categories ) {

				// sort articles into category arrays (if an article is in X categories, it will appear X times in the final array)
				foreach( $categories as $category ) {
					if( array_key_exists( $category->slug, $formatted_array[$article_type] ) ) {
						array_push( $formatted_array[$article_type][$category->slug]['articles'], $data );
					} else {
						$formatted_array[$article_type][$category->slug] = array( 'cat_id' => $category->term_id,
																				  'cat_name' => $category->name,
																				  'cat_slug' => $category->slug,
																				  'cat_description' => $category->description,
																				  'articles' => array( $data ) );
					}
				}

				// sort the categories by description
				uasort( $formatted_array['article'], function( $a, $b ) {
					return intval( $a['cat_description'] ) - intval( $b['cat_description'] );
				});

			}
			// else put in uncategorized
			else {
				$slug = 'uncategorized';

				// add articles to uncategorized "category"
				if( array_key_exists( $slug, $formatted_array[$article_type] ) ) {
					array_push( $formatted_array[$article_type][$slug]['articles'], $data );
				} else {
					$formatted_array[$article_type][$slug] = array( 'cat_id' => FALSE,
																	'cat_name' => FALSE,
																	'cat_slug' => FALSE,
																	'cat_description' => FALSE,
																	'articles' => array( $data ) );
				}
			}
		}
		// categorization not applied or article is featured
		else {
			array_push( $formatted_array[$article_type], $data );
		}

	}

	// return array containing articles in the three categories
	return $formatted_array;

}
endif;

/*****************************************************************************
 * NEWSLETTERS
 *****************************************************************************/

//get all active newsletters
// options per WP get_categories
if ( ! function_exists( 'ff_get_newsletters' ) ) :
function ff_get_newsletters($options=array()) {
	return FF_Newsletter_Common::get_newsletters($options);
}
endif;

// get latest newsletter
if ( ! function_exists( 'ff_get_latest_newsletter' ) ) :
function ff_get_latest_newsletter() {
	return FF_Newsletter_Common::get_latest_newsletter();
}
endif;

// get current newsletter matching the article ID sent through
if ( ! function_exists( 'ff_get_newsletter' ) ) :
function ff_get_newsletter($article_id = NULL) {
	return FF_Newsletter_Common::get_newsletter($article_id);
}
endif;

// get newsletter with matching slug
if ( ! function_exists( 'ff_get_newsletter_by_slug' ) ) :
function ff_get_newsletter_by_slug($slug) {
	return FF_Newsletter_Common::get_newsletter_by_slug($slug);
}
endif;

// get the next and previous newsletter
if ( ! function_exists( 'ff_get_adjacent_newsletters' ) ) :
function ff_get_adjacent_newsletters($current_newsletter=null, $showArchived=false){

	// use latest newsletter if none specified
	if(!$current_newsletter) $current_newsletter = ff_get_latest_newsletter();

	$result = array(
		'previous' 	=> null,
		'current'	=> $current_newsletter,
		'next'		=> null
	);

	// if we have a newsletter then find its siblings
	if($current_newsletter) {

		$result['previous'] = get_adjacent_post(false, '', true);
		$result['next'] 	= get_adjacent_post(false, '', false);

	}

	return $result;
}
endif;

// get current newsletter title
if ( ! function_exists( 'ff_get_newsletter_title' ) ) :
function ff_get_newsletter_title($newsletter=null) {
	return FF_Newsletter_Common::get_newsletter_title($newsletter);
}
endif;

// get current newsletter 'publish date'
if ( ! function_exists( 'ff_get_newsletter_date' ) ) :
function ff_get_newsletter_date($format='j F Y', $newsletter=null) {
	return FF_Newsletter_Common::get_newsletter_date($format, $newsletter);
}
endif;

// get (latest) newsletter URL
if ( ! function_exists( 'ff_get_newsletter_url' ) ) :
function ff_get_newsletter_url($newsletter=null) {
	return FF_Newsletter_Common::get_newsletter_url($newsletter);
}
endif;

// check if an ID is for a newsletter or an article
if ( ! function_exists( 'ff_is_newsletter' ) ) :
function ff_is_newsletter($post=null, $post_type=NULL) {
	return FF_Newsletter_Common::is_newsletter_view($post, $post_type);
}
endif;

/*****************************************************************************
 * CATEGORIES / TAXONOMIES
 *****************************************************************************/

 // TODO: remove when old theme retired
// display a list of tags for an article
if ( ! function_exists( 'ff_the_tags' ) ) :
function ff_the_tags( $ID, $taxonomy='article_tag' ) {
	if(taxonomy_exists($taxonomy)) {
		echo get_the_term_list( $ID, $taxonomy, '<p class="tags"><span class="glyphicon glyphicon-tags" aria-hidden="true" title="Tags"></span> ', ', ', '</p>');
	}
}
endif;

 // TODO: remove when old theme retired
// get the terms used in this issue
if ( ! function_exists( 'ff_get_terms_by_issue' ) ) :
function ff_get_terms_by_issue( $taxonomy, $issue_id ) {
	return FF_Newsletter_Common::get_terms_by_issue($taxonomy, $issue_id);
}
endif;