<?php
/**
 * The common template region for the Footer.
 *
 * Contains the closing of the #main div and all content
 *
 * @package WordPress
 * @subpackage Firefly
 * @since Firefly Base 2.0
 */
?>

			</div><!-- #main -->

			<div id="footer-widgets">
				<div class="row">

					<!-- quick links -->
					<div class="quicklinks col-sm-12 col-md-6">
						<h3>Quick Links</h3>
						<?php wp_nav_menu(array(
							'theme_location' => 'quicklinks',
							'depth' 		 => 1
						)); ?>
					</div>

					<!-- contact us -->
					<div class="contact col-xs-12 col-sm-6 col-md-3">
						<h3>Contact Us</h3>
						<p>
							<a class="icon" href="tel:+61299093133">
								<i class="fa fa-phone"></i>
								(02) 9280 0101
							</a>
						</p>
						<p>
							<a class="icon" href="">
								<i class="fa fa-envelope-o"></i>
								info@fi.net.au
							</a>
						</p>
						<p>
							<a class="icon" href="">
								<i class="fa fa-mouse-pointer"></i>
								info@fi.net.au
							</a>
						</p>
						<p>
							<a class="icon" href="" target="_blank">
								<i class="fa fa-map-marker"></i>
								C3.03 22-36 Mountain St<br>
								Ultime NSW 2007
							</a>
						</p>
					</div>

					<!-- social -->
					<div class="social col-xs-12 col-sm-6 col-md-3">
						<h3>Stay in touch</h3>
						<?php ff_social_links(); ?>
					</div>

				</div><!-- /.row -->
			</div><!-- /#footer-widgets -->

			<div id="footer">
				<div class="row">
					<!-- copyright -->
					<div class="col-sm-6">
						<span>&copy; <?php echo date('Y'); ?> Buzz Portal</span>
					</div>
					<!-- Credit-->
					<div class="col-sm-6">
						<a class="credit" href="http://www.fi.net.au" title="Website by Firefly Interactive">Powered by The Buzz</a>
					</div>
				</div>
			</div><!-- #footer -->

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
