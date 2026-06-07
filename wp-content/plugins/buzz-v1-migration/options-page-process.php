<?php

$report = array();

// process selected newsletters
if( isset( $_REQUEST['newsletter'] ) ) {

	// this is the list of term_ids to process
	$ids = $_REQUEST['newsletter'];

	foreach($ids as $term_id) {
		$r = array('id'=>$term_id, 'name' => '', 'success'=>false, 'message' => '');
		$term = get_term($term_id, 'newsletter');

		if(is_wp_error($term)) {
			$r['message'] = 'invalid term';
			$report[] 	  = $r;
			continue;
		}

		/////////////////////////////////////////////
		// EXTRACT DATA
		/////////////////////////////////////////////

		$title  		= $term->name;
		$slug 			= $term->slug;
		$id     		= $term->term_id;
		$count  		= $term->count;
		$publish_date	= Buzz_V1_Migration_Options_Page::get_tax_meta($id, 'publish_date');
		$thumb 			= Buzz_V1_Migration_Options_Page::get_tax_meta($id, 'thumbnail');

		$r['name'] 		= $title;

		// see if there's already a post of type "newsletter" with this same slug
		$queried_post = get_page_by_path($slug, OBJECT,'newsletter');

		// if it DOES already exist then skip
		if($queried_post) {
			$r['message'] = 'already exists - skipped';
			$report[] 	  = $r;
			continue;
		}

		// get a list of articles belonging to the term.
		$args = array(
	        'post_type' 		=> 'article',
	        'post_status' 		=> 'publish',
	        'posts_per_page' 	=> -1,
			'newsletter'		=> $slug
	    );

	    $q_articles = new WP_Query($args);

		// if no articles then skip
		if($q_articles->post_count <= 0) {
			$r['message'] = 'no articles - skipped';
			$report[] 	  = $r;
			continue;
		}

		// array of associated articles
		$articles = $q_articles->posts;

		/////////////////////////////////////////////
		// CREATE NEWSLETTER POST
		/////////////////////////////////////////////

		$newsletter = array(
			'post_name'			=> $slug,
			'post_title'		=> $title,
			'post_status'		=> 'publish',
			'post_type'			=> 'newsletter'
		);

		$date = strtotime($publish_date);
		if($date !== false) {
			$newsletter['post_date'] = date('Y-m-d H:i:s', $date);
		}

		$newsletter_id = wp_insert_post($newsletter, false);

		if(!$newsletter_id) {
			$r['message'] = 'unable to create newsletter post - bad params';
			$report[]     = $r;
			continue;
		}

		// set featured image
		if(!empty($thumb)) {
			set_post_thumbnail($newsletter_id, $thumb['id']);
		}

		/////////////////////////////////////////////
		// ASSIGN NEWSLETTER PARENT TO ARTICLES
		/////////////////////////////////////////////

		foreach($articles as $article) {
			// set parent ID
			update_post_meta($article->ID, 'ff_parent_id', $newsletter_id);
			// get metadata
			$feature = get_post_meta($article->ID, 'wpcf-feature', true);
			$email   = get_post_meta($article->ID, 'wpcf-feature-email', true);

			// kill any old metadata form previous tests etc
			delete_post_meta($article->ID, 'ff_featured_email');
			delete_post_meta($article->ID, 'ff_featured_article');

			// set article v2 metadata
			if(!empty($email)) {
				update_post_meta($article->ID, 'ff_featured_email', 'featured');
			}
			if(!empty($feature)) {
				update_post_meta($article->ID, 'ff_featured_article', 'featured');
			}
		}


		// update report
		$r['success']	= true;
		$report[] 		= $r;
	}

	foreach($report as $r) {
		if($r['success']) {
			printf('<div class="updated"><p>%s : Done!</p></div>', $r['name']);
		} else {
			printf('<div class="error"><p>%s : %s</p></div>', $r['name'], $r['message']);
		}
	}

}

