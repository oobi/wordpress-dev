<?php 
/*********************************************************************
 * Sidebar containing a list of newsletter articles
 *
 * This file must be called using include( locate_template() ) 
 * AFTER the following variables have been set:
 * 	- $show_share_links (bool)
 * 	- $list_options (array)
 *********************************************************************/

// defaults 
if( !isset( $show_share_links ) ) 	{ $show_share_links = false; }
if( !isset( $list_options ) ) 		{ $list_options = array(); } ?>

<div id="sidebar" class="col-sm-4 col-md-3">

	<?php // Share links
	if( $show_share_links ) { ff_share_links( TRUE ); } ?>

	<div class="this-issue hidden-xs">
		<h3>In This Issue</h3>
		<?php
			// print article list
			$newsletter = ff_get_newsletter( get_the_ID() );
			$list = ff_get_article_category_list( $newsletter, $list_options );
			
			echo '<ul>';

			// taxonomies are active
			if( $list['taxonomies_active'] ) {

				foreach( $list['articles'] as $category ) {

					// built article list HTML
					$category_articles = '';
					foreach( $category['articles'] as $article ) {
						$category_articles .= sprintf( '<li class="article-item %s %s"><a href="%s">%s%s</a></li>',
							$article['featured'] ? 'featured' : '',
							ff_is_current_article( $post, $article['id'] ) ? 'current' : '',
							$article['permalink'],
							$article['featured'] ? '<i class="icon fa fa-star"></i>' : '',
							$article['title']
						);
					}

					// check if articles are categorized or uncategorized
					if( $category['slug'] ) { // categorized

						// build category HTML, include article list HTML
						printf( '<li class="category %s"><h4 class="category-name">%s</h4><ul>%s</ul></li>',
								$category['slug'],
								$category['name'],
								$category_articles
						);

					} else { // uncategorized

						// build category HTML, include article list HTML
						printf( '<li class="category uncategorized"><ul>%s</ul></li>',
							$category_articles
						);

						//echo $category_articles;
					}

				}

			}
			// taxonomies are not active
			else {
				foreach( $list['articles'] as $article ) {
					printf( '<li class="article-item %s %s"><a href="%s">%s%s</a></li>',
						$article['featured'] ? 'featured' : '',
						ff_is_current_article( $post, $article['id'] ) ? 'current' : '',
						$article['permalink'],
						$article['featured'] ? '<i class="icon fa fa-star"></i>' : '',
						$article['title']
					);
				}
			}
			echo '</ul>';
		?>
	</div>

	<?php if ( is_active_sidebar( 'sidebar_widget_1' ) ) : ?>
	<div class="row">
		<div class="col-xs-12">
			<?php dynamic_sidebar( 'sidebar_widget_1' ); ?>
		</div>
	</div>
	<?php endif; ?>

</div><!-- #sidebar -->

<?php // remove defaults (to prevent affecting other includes)
unset( $show_share_links );
?>