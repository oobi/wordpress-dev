<?php
/**
 * The common template region for the Footer.
 *
 * Contains the closing of the #main div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

					<div class="clear"></div>
				</div><!-- #main .inner -->
			</div><!-- #main -->

			<?php
			/***********************************************
			 * WIDGET AREA 
			 ***********************************************/

			get_template_part( 'blocks/footer/widget-area' ); 
			
			/***********************************************
			 * FOOTER 
			 ***********************************************/

			get_template_part( 'blocks/footer/footer' );
			?>
			
		</div><!-- #wrapper .inner -->
	</div><!-- #wrapper -->

	<?php
		/* IMPORTANT
		 * Always call wp_footer() just before the closing body
		 * tag of your theme. Otherwise you will break many plugins
		 * which can use this hook to reference JavaScript files. */
		wp_footer();
	?>
</body>
</html>
