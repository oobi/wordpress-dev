<?php

class Buzz_Addon_Email_View
{

	// the rewrite rule used to access the email template
	private $rewrite_rule = 'newsletter/([^/]+)/email/?.*?$';

	// email CSS registered at runtime
	// orginal themes require these includes for email
	private static $registered_style_handles = array('ff-customiser-style', 'newsletter_email_css', 'theme_specific_email_css');


	/**
	 * Init plugin functionality and hooks
	 */
	public function plugin_init()
	{

		// add options page
		require_once plugin_dir_path(dirname(__FILE__)) . 'buzz-addon-email-view/options-page.php';

		// add custom hooks
		add_action('buzz_newsletter_child_attr', 	array($this, 'newsletter_child_attr'), 		10, 2);
		add_action('buzz_after_article_meta_box', 	array($this, 'article_meta_box'), 			10, 2);
		add_action('buzz_after_save_article_meta', array($this, 'save_article_meta'), 			10, 2);

		// add Wordpress hooks
		add_action('init', 						array($this, 'add_rewrite_rules'), 			10);
		add_action('query_vars', 					array($this, 'add_url_vars'), 				10, 1);
		add_action('manage_edit-article_columns', 	array($this, 'register_custom_columns_article'), 10, 1);
		add_action('manage_article_posts_custom_column', array($this, 'fill_custom_columns_article'), 10, 2);
		add_action('admin_bar_menu', 				array($this, 'add_to_admin_bar'), 			999);
		add_action('admin_notices', 				array($this, 'unsubscribe_admin_notice'), 	10);

		// remove scripts
		add_action('wp_print_scripts', 			array($this, 'remove_all_scripts'), 			1000);

		// some plugins (Gravity Forms, etc) add scripts to the markup using various hooks.
		// Gravity in particular uses output buffering to add scripts to the header.
		// This script cancels the buffering if we are in email view, so that no scripts are output,
		// then cleans up anything that was inserted.
		add_action('template_redirect', function () {
			if (self::is_email_view()) {
				// output buffering to remove scripts
				if (ob_get_level() > 0) {
					ob_end_clean(); // clean output buffer
				}

				ob_start(function ($buffer) {
					// remove scripts from the buffer
					return preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $buffer);
				});
			}
		});

		// remove styles
		add_action('wp_print_styles', 				array($this, 'remove_all_styles'), 			1000);

		// add Wordpress filters
		add_filter('template_include', 				array($this, 'change_template'), 				10);

		// remove embeds
		add_action('wp',							array($this, 'disable_embeds_init'),			9999);

		// remove emoji support as soon as we have an idea of what we're trying to render
		add_action('wp', 							array($this, 'remove_emoji_support'),			9999);

		// remove monster insights scripts
		add_action('wp',							array($this, 'disable_monster_analytics'),		9999);

		// remove disable GA google analytics scripts
		add_action('wp',							array($this, 'disable_ga_google_analytics'),	9999);

		// remove ff global analytics scripts
		add_action('wp',							array($this, 'disable_ff_global_analytics'),	9999);
	}

	/**
	 * The code that runs during plugin activation.
	 */
	public function activate()
	{
		// make sure the rewrite rules have been added
		$this->add_rewrite_rules();

		// flush rules
		flush_rewrite_rules();
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public function deactivate()
	{
		global $wp_rewrite;

		// make sure the rewrite rules have been removed
		unset($wp_rewrite->extra_rules_top[$this->rewrite_rule]);

		// flush rules
		flush_rewrite_rules();
	}

	/**************************************************************************
	 * SET UP EMAIL VIEW FUNCTIONALITY
	 **************************************************************************/

	/**
	 * Add custom query vars
	 */
	public function add_url_vars($public_query_vars)
	{
		array_push($public_query_vars, 'email');
		return $public_query_vars;
	}

	/**
	 * Add 'Email View' link to Wordpress Admin Toolbar
	 */
	public function add_to_admin_bar($wp_admin_bar)
	{
		global $post;

		if ($post && FF_Newsletter_Common::is_newsletter_view($post)) {

			// newsletter issue currently being viewed
			$current_newsletter = FF_Newsletter_Common::get_newsletter($post->ID);

			// Show 'Email View' button if in Browser View
			if ($current_newsletter) {
				if (!self::is_email_view()) {
					$emailLink = self::get_email_url($current_newsletter->ID); // get email link
					$args = array(
						'id'    => 'email_view',
						'title' => __('Email View', 'ff_newsletter'),
						'href'  => $emailLink,
						'meta'  => array('class' => 'email-view', 'target' => '_blank')
					);
					$wp_admin_bar->add_node($args);
				}
				// Show 'Browser View' button if in Email View
				else {
					$args = array(
						'id'    => 'browser_view',
						'title' => __('Back to Browser View', 'ff_newsletter'),
						'href'  => get_permalink($current_newsletter->ID),
						'meta'  => array('class' => 'browser-view')
					);
					$wp_admin_bar->add_node($args);
				}
			}
		}
	}


	/**
	 * get the newsletter email URL
	 */
	public static function get_email_url($newsletter_id)
	{
		$newsletter_status 	= get_post_status($newsletter_id);
		$url_param 			= 'email/';

		if ($newsletter_status == 'draft') {
			$url_param = '&email=1';
		}

		return $newsletter_id ? get_permalink($newsletter_id) . $url_param : NULL;
	}

	/**
	 * Add rewrite rules to redirect query vars to email view
	 */
	public function add_rewrite_rules()
	{
		add_rewrite_tag('%email%', '([^&]+)');
		add_rewrite_rule($this->rewrite_rule, 'index.php?newsletter=$matches[1]&email=1', 'top');
	}

	/**
	 * Change to print template if URL variable is present in request
	 */
	public function change_template($template)
	{
		$filename = basename($template, '.php');

		// if the email flag is set, then add '-email' suffix to the chosen template if it exists
		if (self::is_email_view()) {
			$templates = apply_filters('buzz_email_template', array($filename . '-email.php', $template, 'index.php'));

			$new_template = locate_template($templates);
			if ($new_template != '') {
				return $new_template;
			}
		}

		// not found - return original template
		return $template;
	}

	/**
	 * Remove the wp-embed script hook from the footer to prevent it showing up in the email view.
	 * This is hooked into "parsr_query" since this is the earliest point at which we know whether this is a request for an email view
	 * @see https://make.wordpress.org/core/2015/10/28/new-embeds-feature-in-wordpress-4-4/
	 * "Disabling the feature"
	 */
	public function disable_embeds_init()
	{
		if (self::is_email_view()) {
			/* @var WP $wp */
			global $wp;

			// Remove the embed query var.
			$wp->public_query_vars = array_diff($wp->public_query_vars, array(
				'embed',
			));

			// Remove the REST API endpoint.
			remove_action('rest_api_init', 'wp_oembed_register_route');

			// Turn off oEmbed auto discovery.
			add_filter('embed_oembed_discover', '__return_false');

			// Don't filter oEmbed results.
			remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

			// Remove oEmbed discovery links.
			remove_action('wp_head', 'wp_oembed_add_discovery_links');

			// Remove oEmbed-specific JavaScript from the front-end and back-end.
			remove_action('wp_head', 'wp_oembed_add_host_js');
			//add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );

			// Remove all embeds rewrite rules.
			//add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
		}
	}

	/**
	 * Email view needs no JavaScript
	 */
	public function remove_all_scripts()
	{
		global $wp_scripts;

		if (self::is_email_view()) {
			$wp_scripts->queue = array();
		}
	}

	/**
	 * Email view needs no CSS except its own and the customiser
	 * Dequeue anything else that wasn't registered as an email style
	 */
	public function remove_all_styles()
	{
		global $wp_styles;

		if (self::is_email_view()) {
			$wp_styles->queue = self::$registered_style_handles;
		}
	}

	/**
	 * Disable Emoji scripts/styles
	 */
	public function remove_emoji_support()
	{
		if (self::is_email_view()) {
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			// remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action('wp_print_styles', 'print_emoji_styles');
			// remove_action( 'admin_print_styles', 'print_emoji_styles' );
		}
	}

	/**
	 * Disable Monster Insights scripts
	 */
	public function disable_monster_analytics()
	{
		if (self::is_email_view()) {
			remove_action('wp_head', 'monsterinsights_tracking_script', 6);
		}
	}

	/**
	 * Disable GA Google Analytics scripts
	 */
	public function disable_ga_google_analytics()
	{
		if (self::is_email_view()) {
			remove_action('wp_head', 'ga_google_analytics_tracking_code');
		}
	}

	/**
	 * Disable Firefly Global Analytics scripts
	 */
	public function disable_ff_global_analytics()
	{
		if (class_exists('Multisite_Global_Analytics')) {
			$plugin = Multisite_Global_Analytics::get_instance();
			if (self::is_email_view()) {
				remove_action('wp_head',	array($plugin, 'output_analytics'));
				remove_action('wp_footer', array($plugin, 'output_analytics'));
			}
		}
	}

	/**
	 * Register the email CSS handle
	 */
	public static function register_style($handle, $src, $deps = NULL, $ver = false, $media = 'all')
	{
		if (self::is_email_view()) {
			wp_register_style($handle, $src, $deps, $ver, $media);

			if (!in_array($handle, self::$registered_style_handles)) {
				self::$registered_style_handles[] = $handle;
			}
		}
	}

	/**
	 * Enqueue the email CSS handle
	 */
	public static function enqueue_style($handle, $src, $deps = NULL, $ver = false, $media = 'all')
	{
		if (self::is_email_view()) {
			wp_enqueue_style($handle, $src, $deps, $ver, $media);

			if (!in_array($handle, self::$registered_style_handles)) {
				self::$registered_style_handles[] = $handle;
			}
		}
	}

	/**
	 * Determine if email view
	 */
	public static function is_email_view()
	{
		global $wp_query, $post;
		$is_newsletter = ($post && FF_Newsletter_Common::is_newsletter_view($post));
		return ($is_newsletter && !is_admin() && isset($wp_query->query_vars['email']));
	}

	/**
	 * Return registered style handles
	 */
	public static function get_registered_style_handles()
	{
		return self::$registered_style_handles;
	}

	/**
	 * Format the WP Query post object to be displayed in Email View
	 *
	 * @param 	$all_articles 		{Object}	The WP Query object containing all articles in a newsletter
	 * @param 	$num_featured 		{Int}		The maximum number of featured articles to include
	 * @param 	$show_thumbs 		{Boolean}	Whether to show thumbnails for index articles
	 *
	 * @return 	$formatted_array	{Array}		A categorised array containing every article
	 */
	public static function format_email_article_array($all_articles = NULL, $num_featured = 1, $show_thumbs = true, $categorize = false)
	{

		// return if no post object passed
		if (!$all_articles) {
			return NULL;
		}

		// get articles from post object
		$articles = $all_articles->posts;

		// create loop variables
		$formatted_array = array('featured' => array(), 'in-email' => array(), 'no-thumb' => array());
		$count_featured = 0;

		// loop through articles
		foreach ($articles as $article) {
			// get article variables
			$id 			= $article->ID;
			$title 			= $article->post_title;
			$permalink 		= get_the_permalink($article->ID);
			$has_thumbnail	= has_post_thumbnail($article->ID);
			$metadata 		= get_metadata('post', $id);
			$is_featured	= array_key_exists('ff_featured_article', $metadata);
			$is_in_email	= array_key_exists('ff_featured_email', $metadata);

			// if we are categorizing the articles and the taxonomy exists (ie. plugin is active), get the categories
			$has_categories = false;
			if ($categorize && taxonomy_exists('article_category')) {
				$categories 	= get_the_terms($article, 'article_category');
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
			$data['has_thumb'] 	= $has_thumbnail;
			$data['is_featured'] = false; // overridden later if true

			// determine which category the article should be put in depending on post meta status
			if ($is_in_email) {
				$article_type = 'in-email';

				// if article is in-email and also featured, change article type to featured
				if ($is_featured) {
					$data['is_featured'] = true;

					// if max number of features reached, don't change type
					if (++$count_featured <= $num_featured) {
						$article_type = 'featured';
					}
				}

				// if article has no featured image OR show_thumbs is false
				// AND is not featured, AND is not being categorized, change to no-thumb
				if ((!$has_thumbnail ||  !$show_thumbs) && !$is_featured && !$categorize) {
					$article_type = 'no-thumb';
				}

				// if categorization and not featured
				if ($categorize && $article_type !== 'featured') {

					// if article has a category
					if ($has_categories) {

						// sort articles into category arrays (if an article is in X categories, it will appear X times in the final array)
						foreach ($categories as $category) {
							if (array_key_exists($category->slug, $formatted_array[$article_type])) {
								array_push($formatted_array[$article_type][$category->slug]['articles'], $data);
							} else {
								$formatted_array[$article_type][$category->slug] = array(
									'cat_id' => $category->term_id,
									'cat_name' => $category->name,
									'cat_slug' => $category->slug,
									'cat_description' => $category->description,
									'articles' => array($data)
								);
							}
						}

						// sort the categories by description
						uasort($formatted_array['in-email'], function ($a, $b) {
							return intval($a['cat_description']) - intval($b['cat_description']);
						});
					}
					// else put in uncategorized
					else {
						$slug = 'uncategorized';

						// add articles to uncategorized "category"
						if (array_key_exists($slug, $formatted_array[$article_type])) {
							array_push($formatted_array[$article_type][$slug]['articles'], $data);
						} else {
							$formatted_array[$article_type][$slug] = array(
								'cat_id' => FALSE,
								'cat_name' => FALSE,
								'cat_slug' => FALSE,
								'cat_description' => FALSE,
								'articles' => array($data)
							);
						}
					}
				}
				// categorization not applied or article is featured
				else {
					array_push($formatted_array[$article_type], $data);
				}
			}
		}

		// return array containing articles in the three categories
		return $formatted_array;
	}

	/**************************************************************************
	 * UNSUBSCRIBE FUNCTIONALITY
	 **************************************************************************/

	/**
	 * Show admin message if unsubscribe field is not set on the Options page
	 */
	public function unsubscribe_admin_notice()
	{

		$has_unsubscribe = self::is_unsubscribe_set();

		// if unsubscribe option is not set, show message
		if (!$has_unsubscribe) {
			$class 		= "update-nag";
			$message 	= self::unsubscribe_not_set_message();
			echo 		"<div class=\"$class\"> <p>$message</p></div>";
		}
	}

	/**
	 * Checks if Unsubscribe link is set on the Options Page
	 */
	public static function is_unsubscribe_set()
	{
		// get unsubscribe link
		$settings = get_option('email_view_settings');

		// check if unsubscribe is set
		if (!isset($settings['unsubscribe_link']) || empty($settings['unsubscribe_link']) || $settings['unsubscribe_link'] === '') {
			return false;
		} else {
			return $settings['unsubscribe_link'];
		}
	}

	/**
	 * Outputs the unsubscribe link set in the Email View Options Page
	 */
	public static function the_unsubscribe_link()
	{
		echo self::get_unsubscribe_link();
	}

	/**
	 * Outputs the unsubscribe link set in the Email View Options Page
	 */
	public static function get_unsubscribe_link()
	{

		$has_unsubscribe = self::is_unsubscribe_set();

		// if unsubscribe link is set, output
		if ($has_unsubscribe) {
			return html_entity_decode($has_unsubscribe);
		}

		return '';
	}

	/**
	 * The message to display when the unsubscribe link is not set. Used in both the theme and back end.
	 */
	public static function unsubscribe_not_set_message()
	{
		return sprintf('The Email View has been disabled because <b>an unsubscribe link has not been set up</b>.
        <a href="%s">Go to Email View Settings</a>', admin_url('options-general.php?page=email_view_settings'));
	}

	/**************************************************************************
	 * NEWSLETTER META BOX
	 **************************************************************************/

	/**
	 *
	 */
	public function newsletter_child_attr($child)
	{

		$is_feat_email = esc_attr(get_post_meta($child->ID, 'ff_featured_email', true));
		printf(
			'<span class="icon-email tick %1$s" title="%2$s" data-property="%3$s" data-value="%4$s"></span>',
			$is_feat_email ? 'active' : 'inactive',
			__('Included in Email', 'ff_newsletter'),
			'ff_featured_email',
			'featured'
		);
	}

	/**************************************************************************
	 * ARTICLE META BOX
	 **************************************************************************/

	/**
	 * Add checkbox for email view to the article meta box
	 *
	 * Extends article_meta_box() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
	 */
	public function article_meta_box($object, $box)
	{
		global $pagenow; ?>

		<p>
			<?php
			// get featured in-email flag
			$is_feat_email = esc_attr(get_post_meta($object->ID, 'ff_featured_email', true));

			// determine whether checkbox should be checked. Checked if flag set and if a new post (on by default)
			$is_new_post = ($pagenow == 'post-new.php');
			$is_checked = !empty($is_feat_email) || $is_new_post; ?>

			<input type="checkbox" name="ff-featured-email" id="ff-featured-email" value="featured" <?php echo $is_checked ? 'checked' : ''; ?> />
			<label for="ff-featured-email">
				<?php _e("Included in Email View", 'ff_newsletter'); ?>
			</label>
		</p>
<?php
	}

	/**
	 * Save the email checkbox added in article_meta_box().
	 *
	 * Extends save_article_meta() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
	 */
	public function save_article_meta($post_id, $post)
	{

		// Get the posted data and sanitize it for use as an HTML class.
		$new_meta_value_email 	= (isset($_POST['ff-featured-email']) 	? sanitize_html_class($_POST['ff-featured-email']) 	: '');

		// add/update or delete values accordingly for all settings
		FF_Newsletter_Common::save_meta_values($post_id, 'ff_featured_email',		$new_meta_value_email);
	}

	/**************************************************************************
	 * ARTICLE COLUMNS
	 **************************************************************************/

	/**
	 * Register custom columns for Article post type
	 *
	 * Extends register_custom_columns_article() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
	 */
	public function register_custom_columns_article($columns)
	{

		$columns['in_email'] = __('Displayed in Email', 'ff_newsletter');
		return $columns;
	}

	/**
	 * Fill custom columns in Article list view.
	 *
	 * Extends fill_custom_columns_article() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
	 */
	public function fill_custom_columns_article($column_name, $post_id)
	{

		// get article attributes
		$is_feat_email = esc_attr(get_post_meta($post_id, 'ff_featured_email', true));

		// output in appropriate column
		$output = "";
		if ($column_name == 'in_email') {
			$output = $is_feat_email ? "<span class='icon-email tick' title='" . __('Included in Email', 'ff_newsletter') . "'></span>" : "";
		}

		echo $output;
	}
}
