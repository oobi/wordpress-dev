<?php
/**
 * Template Name: Homepage
 * Description: Home page template.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>

	<?php if ( have_posts() ) : ?>

		<div class="hero-wrapper">
			<?php echo get_the_post_thumbnail( $post->ID, 'home-hero' ); ?>
		</div>

		<?php while ( have_posts() ) : the_post(); // start loop ?>
			<div id="content" class="wide col-xs-12">
				<div class="row">
					<div class="col-xs-12">
						<div class="home-content">
							<h1 class="page-title"><span>Accessing My Portal</span></h1>
							<?php the_content(); ?>
						</div>

						<div class="row portal-links">
							<?php
								if(function_exists('CFS')) {
									$pages = CFS()->get('portal_page_links');
									if( count( $pages ) != 0 ) {
										foreach($pages as $page_id) {
									    	$page = get_post($page_id);
									    	$title = $page->post_title;
									    	$thumb = get_the_post_thumbnail($page_id, 'portal-link');
									    	$permalink = get_the_permalink($page_id);

									    	printf('<div class="portal-link col-md-4"><div class="inner">
									    				<a href="%s"><h2>%s</h2>%s</a>
									    			</div></div>',
									    			$permalink,
									    			$title,
									    			$thumb);
									    }
									}
								}
							?>
						</div><!-- /.portal-links -->
					</div><!-- /.col-xs-12 -->
				</div><!-- /.row -->
			</div><!-- /#content -->

		<?php endwhile; // end loop ?>

		<div class="clear"></div>

	<?php endif; // end conditional ?>

<?php get_footer(); ?>
