<?php
/*********************************************************************
 * The newsletter navbar
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $post (WP post object)
 *********************************************************************/

// this is for posts onlys
if( $post ) :

	$current_newsletter = ff_get_newsletter($post->ID); // newsletter issue currently being viewed

	// am I a newsletter page?
	$is_newsletter 		= ff_is_newsletter() || isset($wp_query->query_vars['issue']);
	// is this the email view?
	$is_email_view 		= isset($wp_query->query_vars['email']);
	// am I an article archvie of the relevant taxonomies?
	$is_article_archive = is_tax( array('article_category', 'article_tag') );
	// should category URLs use the issue parameter?
	$issue_param		= $is_newsletter || isset($wp_query->query_vars['issue']) ? "issue/{$current_newsletter->post_name}" : '';
	// print plugin is active
	$is_print_active	= defined( 'BUZZ_ADDON_PRINT_VIEW' );
	// get print link
	if( $is_print_active ) {
		$printLink = ff_get_print_url($current_newsletter->ID);
	}
	// Check issue title setting, display if on
	$show_issue_title = get_theme_mod( 'ff_issue_title_display' );
?>
<!-- main navigation -->
<nav id="nav" class="navbar">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">

			<?php if($is_newsletter) : ?>
			<p class="visible-xs-inline-block navbar-text navbar-right date">
				<?php if( $show_issue_title ) : ?>
					<span class="current-issue"><?php echo ff_get_newsletter_title( $current_newsletter ); ?></span>
					<span class="divider">&mdash;</span>
				<?php endif; ?>
				<span class="issue-date"><?php echo ff_get_newsletter_date('d M Y', $current_newsletter); ?></span>
			</p>
			<?php endif; ?>

			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#ff-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<div id="ff-navbar-collapse" class="collapse navbar-collapse">
			<?php if($is_newsletter) : ?>
				<p class="navbar-text navbar-left hidden-sm hidden-xs date">

					<?php if( $show_issue_title ) : ?>
						<span class="current-issue"><?php echo ff_get_newsletter_title($current_newsletter); ?></span>
						&mdash;
					<?php endif; ?>

					<?php
					// Check date format string, display in selected format
					$date_format = get_theme_mod( 'ff_date_format' );
					if( !$date_format ) {
						$date_format = 'd M Y';
					} ?>
					<span class="issue-date"><?php echo ff_get_newsletter_date( $date_format, $current_newsletter ); ?></span>
				</p>
			<?php endif; ?>

			<ul class="nav navbar-nav">

				<div class="visible-xs handset-nav-top">
					<?php ff_share_links( ); ?>
					<?php get_search_form(); ?>
				</div>

				<!-- index link -->
				<li class="nav-index">
					<a href="<?php echo ff_get_newsletter_url($current_newsletter); ?>">Index</a>
				</li>

				<?php if( !$is_email_view ) :
					// ARCHIVE LINK ?>
					<li class="nav-archive"><a data-type="archive" href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>" title="View past newsletter issues">Archive</a></li>
					
					<?php
					// PRINT LINK - only show if you have the link and plugin is enabled
					if( $is_print_active && $printLink && $is_newsletter ) : ?>
						<li class="nav-print"><a href="<?php echo $printLink; ?>" target="_blank" title="Get the whole issue in printer-friendly format">Print Issue</a></li>
					<?php endif; ?>

				<?php // EMAIL VIEW ONLY - view full newsletter
				else : ?>
				   	<li class="nav-newsletter"><a href="<?php echo ff_get_newsletter_url($current_newsletter); ?>">View Full Newsletter</a></li>
				<?php endif; ?>

				<?php
					// this is the taxonomy list - only show this if the taxonomy is defined
					// or it will throw errors
					$tax = 'article_category';
					if( taxonomy_exists($tax) && ($is_article_archive || $is_newsletter )) : ?>
					<li class="nav-categories dropdown hidden-xs">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Categories <span class="caret"></span></a>
						<ul class="dropdown-menu">

							<?php
							$categories = ff_get_terms_by_issue( $tax, $current_newsletter->ID );
							foreach( $categories as $category ) {
								printf( '<li><a href="%s%s">%s</a></li>',
									get_term_link( intval( $category->term_id ), $tax ),
									$issue_param,
									$category->name );
							}

							?>

						</ul>
					</li>
				<?php endif; ?>

				<?php // optionally show navbar menu if set 
				if( has_nav_menu( 'navbar-menu' ) ) { 
					wp_nav_menu( array( 'theme_location' 	=> 'navbar-menu',
										'container'			=> false,
										'items_wrap' 		=> '%3$s' // remove wrapping UL
								) ); 
				} ?>

			</ul>

			<div class="hidden-xs">
				<?php get_search_form(); ?>
			</div>
		</div><!-- /.navbar-collapse -->

</nav><!--/#navbar -->

<?php if($is_newsletter) : ?>
	<div class="visible-sm alt-issue-date">
		<?php if( $show_issue_title ) : ?>
			<span class="current-issue"><?php echo ff_get_newsletter_title($current_newsletter); ?></span>
			&mdash;
		<?php endif; ?>
		<span class="issue-date"><?php echo ff_get_newsletter_date('d M Y', $current_newsletter); ?></span>
	</div>
<?php endif; ?>

<?php endif; ?>