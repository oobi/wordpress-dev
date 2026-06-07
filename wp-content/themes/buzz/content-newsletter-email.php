<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php language_attributes(); ?> xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
	<title><?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 */
		global $page, $paged;

		wp_title('|', true, 'right');

		// Add the blog name.
		bloginfo('name');

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo('description', 'display');
		if($site_description &&(is_home() || is_front_page())) {
			echo " | $site_description";
		}

		// Append a page number if necessary:
		if($paged >= 2 || $page >= 2) {
			echo ' | ' . sprintf(__('Page %s', 'firefly'), max($paged, $page));
		}
	?></title>

	<?php wp_head(); ?>
</head>

<body bgcolor="#<?php background_color(); ?>" class="email-view">
	<table id="wrapper" border="0" cellpadding="0" cellspacing="0" align="center" width="640">

		<?php
		 /***************************************************************************
		 * VIEW IN BROWSER
		 ***************************************************************************/ ?>

		<?php $current_newsletter = ff_get_newsletter($post->ID); // newsletter issue currently being viewed ?>
		<tr><td id="view-in-browser">
			Is this email not displaying correctly? <a href="<?php echo ff_get_newsletter_url( $current_newsletter ); ?>">View it in your browser</a>
		</td></tr>

		<?php
		 /***************************************************************************
		 * BANNER
		 ***************************************************************************/
		
		get_template_part( 'blocks/email/banner' );

		/***************************************************************************
		 * FEATURED IMAGE
		 ***************************************************************************/

		get_template_part( 'blocks/email/featured-image' );

		/***************************************************************************
		 * MAIN NAV
		 ***************************************************************************/ 
		 
		get_template_part( 'blocks/email/navbar' ); 

		 /***************************************************************************
		 * ARTICLES
		 ***************************************************************************/ ?>

		<tr><td id="main">

			<?php
			while ( have_posts() ) : the_post(); // start main loop

				// Get all articles
				$args = array(
					'parent-id'			=> get_the_ID()
				);
				$all_articles = ff_get_article_query($args);

				// Format the articles array to be displayed in the email
				$articles = Buzz_Addon_Email_View::format_email_article_array( $all_articles, 2 );

				/***********************************************
				 * Display Featured articles
				 ***********************************************/

				include( locate_template( 'blocks/email/articles-featured.php' ) );

				/***********************************************
				 * Display Non-Featured articles
				 ***********************************************/

				include( locate_template( 'blocks/email/articles-index-2col.php' ) );

				/***********************************************
				* Display Articles with no Featured Image
				***********************************************/

				include( locate_template( 'blocks/email/articles-nothumb.php' ) );

			endwhile; // end main loop ?>

		</td><!-- #main --></tr>

		<?php
		 /***************************************************************************
		 * FOOTER
		 ***************************************************************************/ 

		 get_template_part( 'blocks/email/footer' );

		 /***************************************************************************
		 * UNSUBSCRIBE
		 ***************************************************************************/ ?>

		<tr><td id="unsubscribe" valign="top">
			<?php Buzz_Addon_Email_View::the_unsubscribe_link(); ?>
		</td></tr>

	</table><!-- #wrapper -->
<?php wp_footer(); ?>
</body>
</html>