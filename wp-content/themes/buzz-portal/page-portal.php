<?php
/**
 * Template Name: Portal
 * Description: Portal page template.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

<?php get_header(); ?>



	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); // start loop ?>

			<div class="hero-wrapper">
				<?php echo get_the_post_thumbnail( $post->ID, 'home-hero' ); ?>
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>

			<div id="urgent-messages" class="clearfix">
				<h2><i class="fa fa-bullhorn" aria-hidden="true"></i> Urgent Messages</h2>
				<ul>
					<?php // get posts in category urgent
					$args = array(
						'post_type'		=> 'post',
						'post_status'	=> 'publish',
						'category_name'	=> 'urgent'
					);
					$urgent_messages = get_posts( $args );
					foreach( $urgent_messages as $message ) : ?>
						<li><a href="<?php echo get_the_permalink( $message->ID ); ?>">
							<i class="fa fa-chevron-right" aria-hidden="true"></i> <?php echo $message->post_title; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div><!-- /urgent-messages -->

			<div id="content" class="wide col-xs-12">



				<div id="portal-links" class="clearfix">
					<?php // get portal links
					$links = CFS()->get( 'portal_link_loop' );

					// display portal links
					foreach( $links as $link ) {

						printf( '<a href="%s" %s class="portal-link %s">%s <h3><i class="fa %s"></i> %s</h3><p class="description">%s</p></a>',
						$link['portal_link_url'],
						$link['portal_link_target'] ? 'target="_blank"' : '',
						$link['portal_link_image'] ? 'has-image' : 'no-image',
						$link['portal_link_image'] ? wp_get_attachment_image($link['portal_link_image'], 'thumbnail') : '',
						$link['portal_link_icon'],
						$link['portal_link_title'],
						$link['portal_link_description']
						);

					} ?>
				</div><!-- /portal-links -->

				<div id="more-messages" class="clearfix">
					<div class="col-sm-6">
						<div id="category-messages" class="clearfix">
							<?php // get category name
							$category = get_the_category( get_the_ID() );
							$cat_name = $category[0]->name; ?>
							<h2><i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo $cat_name; ?> Messages</h2>
							<ul>
								<?php // get posts in category urgent
								$args = array(
									'post_type'		=> 'post',
									'post_status'	=> 'publish',
									'category_name'	=> $cat_name,
									'posts_per_page'=> 10
								);
								$cat_messages = get_posts( $args );
								foreach( $cat_messages as $message ) : ?>
									<li><a href="<?php echo get_the_permalink( $message->ID ); ?>">
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <?php echo $message->post_title; ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<div class="col-sm-6">
						<div id="all-messages" class="clearfix">
							<h2><i class="fa fa-newspaper-o" aria-hidden="true"></i> Latest Communication</h2>
							<ul>
								<?php // get posts in category urgent
								$args = array(
									'post_type'		=> 'post',
									'post_status'	=> 'publish',
									'posts_per_page'=> 10
								);
								$cat_messages = get_posts( $args );
								foreach( $cat_messages as $message ) : ?>
									<li><a href="<?php echo get_the_permalink( $message->ID ); ?>">
										<i class="fa fa-chevron-right" aria-hidden="true"></i> <?php echo $message->post_title; ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div><!-- /more-messages -->


			</div><!-- #content -->

		<?php endwhile; // end loop ?>

	<?php endif; // end conditional ?>

<?php get_footer(); ?>
