<?php

/**
 * The class to customise the UI of the taxonomies and post types.
 *
 * @package    ff_newsletter
 * @subpackage ff_newsletter/admin
 * @author     Firefly Interactive
 */
class FF_Newsletter_UI
{


	/**************************************************************************************
	 * NEWSLETTER AND ARTICLE LIST TABLES
	 **************************************************************************************/

	/**
	 * Register custom columns for Newsletter post type
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	array		$columns		Array of column names
	 */
	public function register_custom_columns_newsletter($columns)
	{

		$columns['newsletter_articles'] 	= __('Attached Articles', 'ff_newsletter');
		return $columns;
	}

	/**
	 * Fill custom columns in Newsletter list view.
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	string		$column_name	Name of the column
	 * @param	string		$post_id		ID of the array cell as set in register_custom_columns_newsletter()
	 */
	public function fill_custom_columns_newsletter($column_name, $post_id)
	{

		// Get child articles
		$args = array(
			'parent-id' 	=> get_the_ID(),
			'post_status'	=> 'any'
		);
		$children = FF_Newsletter_Common::get_articles($args);

		// output values in appropriate column
		$output = "";
		switch ($column_name) {
			case "newsletter_articles":
				$output = "<a href='" . get_edit_post_link($post_id) . "'>" . count($children) . "</a>";
				break;
			default:
		}

		echo $output;
	}

	/**
	 * Register custom columns for Article post type
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	array		$columns		Array of column names
	 */
	public function register_custom_columns_article($columns)
	{

		// remove comments column
		unset($columns['comments']);

		// Setup Featured Article column
		$columns['featured_article'] 	= __('Featured Article', 'ff_newsletter');
		$columns['parent_id'] 			= __('Newsletter', 'ff_newsletter');

		return $columns;
	}

	/**
	 * Fill custom columns in Article list view.
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	string		$column_name	Name of the column
	 * @param	string		$post_id		ID of the array cell as set in register_custom_columns_newsletter()
	 */
	public function fill_custom_columns_article($column_name, $post_id)
	{

		// get article attributes
		$is_feat_article 	= esc_attr(get_post_meta($post_id, 'ff_featured_article', true));
		$parent_id 		= esc_attr(get_post_meta($post_id, 'ff_parent_id', true));

		// output in appropriate column
		$output = "";
		if ($column_name == 'featured_article') {
			$output = $is_feat_article ? "<span class='icon-featured tick' title='" . __('Featured Article', 'ff_newsletter') . "'></span>" : "";
		}

		if ($column_name == 'parent_id') {
			$newsletter = ff_get_newsletter($parent_id);
			if ($newsletter) {
				$output = sprintf('<a href="%s">%s</a>', "edit.php?post_type=article&parent_id=$newsletter->ID", get_the_title($newsletter->ID));
			} else {
				$output = sprintf('<i>%s</i>', __('Unattached', 'ff_newsletter'));
			}
		}

		echo $output;
	}

	/**
	 * Add a filter to Article list screen to view by newsletter
	 */
	public function custom_article_filter_ui()
	{

		// only add filter to article post type
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'article') {
			// get newsletters
			$args = array('post_status' => 'any');
			$newsletters = ff_get_newsletters($args);

			// populate filter dropdown
?>
			<select id="parent_id" class="postform" name="parent_id">
				<option value=""><?php _e('All Newsletters', 'ff_newsletter'); ?></option>
				<?php
				$current_v = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : '';
				foreach ($newsletters as $n) {
					printf(
						'<option value="%s"%s>%s</option>',
						$n->ID,
						$n->ID == $current_v ? ' selected="selected"' : '',
						$n->post_title
					);
				}
				?>

			</select>
		<?php
		}
	}

	/**
	 * Alter the query to filter article list table by parent_id
	 * Only altered when using filter added in custom_article_filter_ui()
	 */
	public function custom_article_filter_query($query)
	{
		global $pagenow;

		// if parent_id query var is present when filtering the article list
		if (
			is_admin() && $query->is_main_query() && $pagenow == 'edit.php'
			&& isset($_GET['parent_id']) && $_GET['parent_id'] != ''
		) {

			// only return articles that match that parent_id
			$query->query_vars['meta_key'] 		= 'ff_parent_id';
			$query->query_vars['meta_value'] 	= intval($_GET['parent_id']);
		}
	}

	/**************************************************************************************
	 * NEWSLETTER AND ARTICLE METABOXES
	 **************************************************************************************/

	/**
	 * Remove default category metabox and add custom metaboxes
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function manage_custom_metaboxes()
	{
		// allow sub-processes to affect this title
		$title = apply_filters('buzz_newsletter_child_meta_box_title', 'Articles');

		// Add custom newsletter meta box
		add_meta_box(
			'newsletter-child-meta-box',      				// Unique ID
			esc_html__($title), 						// Title
			array($this, 'newsletter_child_meta_box'), 	// Callback function - create the HTML for the meta box
			'newsletter',         							// Admin page (or post type)
			'normal',         								// Context
			'default'         								// Priority
		);

		// Add article meta box on the 'add_meta_boxes' hook.
		add_meta_box(
			'article-meta-box',      				// Unique ID
			esc_html__('Article Options'), 		// Title
			array($this, 'article_meta_box'),   	// Callback function - create the HTML for the meta box
			'article',         						// Admin page (or post type)
			'side',         						// Context
			'default'         						// Priority
		);

		// Add article meta box on the 'add_meta_boxes' hook.
		add_meta_box(
			'article-parent-meta-box',      		// Unique ID
			esc_html__('Newsletter Issue'), 		// Title
			array($this, 'article_parent_meta_box'),   // Callback function - create the HTML for the meta box
			'article',         						// Admin page (or post type)
			'side',         						// Context
			'high'         							// Priority
		);

		// other plugins may add additional meta boxes here via a hook
		do_action('buzz_after_custom_meta_box');
	}

	/**
	 * Create the HTML for the article parent meta box.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	2.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function article_parent_meta_box($object, $box)
	{
		global $post;

		wp_nonce_field(basename(__FILE__), 'ff_article_nonce');

		// get the ID of parent newsletter
		$parent_id = get_post_meta($post->ID, 'ff_parent_id', true);

		// if article does not have a parent (ie. new), check for URL param
		if (empty($parent_id)) {
			if (isset($_GET['parent_id'])) {
				$parent_id = $_GET['parent_id'];
			}
			// if all else fails, set to most recent newsletter
			else {
				$recent_posts = wp_get_recent_posts(array('post_type' => 'newsletter', 'numberposts' => 1));
				$parent_id = $recent_posts[0]['ID'];
			}
		}

		?>

		<div class="misc-pub-parent-id">
			<label for="ff-parent-id">Newsletter: </label>
			<span id="parent-id-display"><?php echo get_the_title($parent_id); ?></span>
			<a class="edit-parent-id hide-if-no-js" href="#ff-parent-id">
				<span aria-hidden="true">Edit</span>
				<span class="screen-reader-text">Edit status</span>
			</a>

			<div class="hide-if-js" id="parent-id-select">

				<input id="ff-parent-id" type="hidden" value="<?php echo $parent_id; ?>" name="ff-parent-id">

				<?php
				// display drop-down of newsletters, default to most recent OR parent (if applicable)
				$args = array(
					'numberposts' 		=> -1,
					'post_type' 		=> 'newsletter',
					'sort_column'		=> 'menu_order, post_title',
					'sort_order'		=> 'DESC',
					'post_status'		=> 'any'
				);
				$newsletters = get_posts($args);

				echo '<select name="parent_id_select" id="parent_id_select">';
				foreach ($newsletters as $newsletter) {
					$selected = $newsletter->ID == $parent_id;
					printf(
						'<option value="%s" %s>%s</option>',
						$newsletter->ID,
						$selected ? 'selected="selected"' : '',
						get_the_title($newsletter->ID)
					);
				}
				echo '</select>';
				?>

				<a class="save-parent-id hide-if-no-js button" href="#ff-parent-id"><?php _e('OK', 'ff_newsletter'); ?></a>
				<a class="cancel-parent-id hide-if-no-js button-cancel" href="#ff-parent-id"><?php _e('Cancel', 'ff_newsletter'); ?></a>
			</div>
		</div>

		<p class="misc-pub-section">
			<a href="<?php echo esc_url(home_url()); ?>/wp-admin/post.php?post=<?php echo $parent_id; ?>&action=edit"><?php _e('&laquo; Back to Newsletter', 'ff_newsletter'); ?></a>
		</p>

	<?php }

	/**
	 * Create the HTML for the article meta box.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	2.0.0
	 * @access	public
	 * @param	object		$object			The WordPress post object
	 * @param	array		$box			The metabox array containing data defined in add_meta_box
	 */
	public function article_meta_box($object, $box)
	{
	?>

		<p>
			<?php $is_feat_article = esc_attr(get_post_meta($object->ID, 'ff_featured_article', true)); ?>
			<input type="checkbox" name="ff-featured-article" id="ff-featured-article" value="featured" <?php echo $is_feat_article ? 'checked' : ''; ?> />
			<label for="ff-featured-article"><?php _e("Featured Article", 'ff_newsletter'); ?></label>
		</p>

		<?php
		// call any addon actions
		do_action('buzz_after_article_meta_box', $object, $box);
		?>

<?php }

	/**
	 * Save the article meta box's post data.
	 *
	 * @since	2.0.0
	 * @access	public
	 * @param	int			$post_id		ID of the article
	 * @param	object		$post			The WordPress post object
	 */
	public function save_article_meta($post_id, $post)
	{

		// Verify the nonce before proceeding.
		if (!isset($_POST['ff_article_nonce']) || !wp_verify_nonce($_POST['ff_article_nonce'], basename(__FILE__)))
			return $post_id;

		// Get the post type object.
		$post_type = get_post_type_object($post->post_type);

		// Check if the current user has permission to edit the post.
		if (!current_user_can($post_type->cap->edit_post, $post_id))
			return $post_id;

		// Get the posted data and sanitize it for use as an HTML class.
		$new_meta_value_article = (isset($_POST['ff-featured-article']) ? sanitize_html_class($_POST['ff-featured-article']) : '');
		$new_meta_value_parent 	= (isset($_POST['ff-parent-id']) 	? sanitize_html_class($_POST['ff-parent-id']) 	: '');

		// set the selected newsletter taxonomies to the post
		if (isset($_POST['tax_input']['newsletter'])) {
			wp_set_post_terms($post_id, $_POST['tax_input']['newsletter'], 'newsletter');
		}

		// add/update or delete values accordingly for all settings
		FF_Newsletter_Common::save_meta_values($post_id, 'ff_featured_article', 	$new_meta_value_article);
		FF_Newsletter_Common::save_meta_values($post_id, 'ff_parent_id',			$new_meta_value_parent);

		// call any addon actions
		do_action('buzz_after_save_article_meta', $post_id, $post);
	}

	/**
	 * Create the HTML for the newsletter article meta box. Allows creation of new articles and displays current articles.
	 *
	 * Callback for add_meta_box() function in manage_custom_metaboxes()
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	object		$post			The post object
	 */
	public function newsletter_child_meta_box($post)
	{
		// Output controls
		echo "<p class='article-controls'>";
		echo "<a class='button button-primary button-large' href='" . admin_url() . "post-new.php?post_type=article&parent_id=" . get_the_ID() . "'>Add New Article</a>";
		echo "</p>";

		// Get child articles
		$args = array(
			'parent-id' 	=> get_the_ID(),
			'post_status'	=> 'any',
			'orderby'		=> 'menu_order',
			'order'			=> 'ASC'
		);

		// allow sub-processes to modify this query
		$args = apply_filters('buzz_newsletter_child_meta_box_query_args', $args);

		$children = FF_Newsletter_Common::get_articles($args);

		// output current children
		echo '<ul class="buzz-article-sortable-list buzz-article-default">';
		foreach ($children as $child) {
			echo $this->display_child_article($child, 'li');
		}
		echo '</ul>';

		printf(
			'<i class="buzz-no-articles">%s</i>',
			apply_filters('buzz_newsletter_uncategorised_empty_prompt', __('This newsletter has no associated articles', 'ff_newsletter'))
		);
	}

	/**
	 * AJAX: Update the order of the articles.
	 * Expects a POST object with an 'order' parameter which should be an array of article IDs in the desired order.
	 * @since	3.0.0
	 * @access	public
	 */
	public function update_article_order()
	{
		global $wpdb;
		$articles   = $_POST['articles'] ?? [];
		$offset 	= $_POST['offset'] ?? 0;

		// for each item in 'article' set the position for each ID
		foreach ($articles as $position => $id) {
			$wpdb->update(
				$wpdb->posts,
				array('menu_order' => $position + $offset),
				array('ID' => intval($id), 'post_type' => 'article')
			);
		}
	}

	/**
	 * AJAX: Update an attribute flag (e.g. 'featured' on a given article)
	 * Expects a POST object with an article ID parameter, attribute and new value
	 * @since	3.0.0
	 * @access	public
	 */
	public function update_article_attribute()
	{
		global $wpdb;
		$params = array_merge(array(
			'article_id' 	=> NULL,
			'attribute'		=> NULL,
			'value'			=> NULL
		), $_POST);

		// if we have no article ID or no attribute then do nothing
		if (empty($params['article_id'])  || empty($params['attribute'])) {
			return false;
		}

		// update meta value
		if (!empty($params['value'])) {
			update_post_meta(intval($params['article_id']), $params['attribute'], $params['value']);
		} else {
			delete_post_meta(intval($params['article_id']), $params['attribute']);
		}

		return true;
	}


	/**************************************************************************************
	 * PRIVATE
	 **************************************************************************************/

	/**
	 * Display a child article on the newsletter.
	 *
	 * Used within newsletter_child_meta_box()
	 *
	 * @since	3.0.0
	 * @access	public
	 * @param	object		$child			An object containing the one result from get_children()
	 * @param 	string		$wrapper 		The tag to wrap the output
	 */
	public function display_child_article($child, $wrapper)
	{

		// opening wrapper tag
		printf(
			'<%s id="%s" class="%s" data-id="%d" data-order="%s">',
			$wrapper,
			'article_' . $child->ID,
			'status_' . $child->post_status,
			$child->ID,
			$child->menu_order
		);

		// thumbnail
		echo "<div class='thumbnail'>";
		if (has_post_thumbnail($child->ID)) {
			echo get_the_post_thumbnail($child->ID, 'thumbnail');
		} else {
			echo "<img src='" . plugin_dir_url(__FILE__) . "images/placeholder.png' alt='" . __('No Featured Image', 'ff_newsletter')  . "' height='150' width='170'>";
		}
		echo "</div>";

		// post title
		echo "<h4 class='title'>";
		echo $child->post_status == 'draft' ? "<span class='draft'>"  .  __('Draft', 'ff_newsletter')  .  "</span>" : '';
		echo $child->post_title !== '' 		? $child->post_title : "[" . __('no title', 'ff_newsletter') . "]";
		echo "</h4>";

		// attributes
		$is_feat_article = esc_attr(get_post_meta($child->ID, 'ff_featured_article', true));
		echo "<div class='attributes'>";
		printf(
			'<span class="icon-featured tick %1$s" title="%2$s" data-property="%3$s" data-value="%4$s"></span>',
			$is_feat_article ? 'active' : 'inactive',
			__('Featured Article', 'ff_newsletter'),
			'ff_featured_article',
			'featured'
		);

		// call add actions
		do_action('buzz_newsletter_child_attr', $child);

		// add spinner
		echo '<div class="spinner"></div>';

		echo "</div>";

		// Edit and View controls
		echo "<div class='row'>";
		echo "<a class='edit button button-primary button-small' href='" . get_edit_post_link($child->ID) . "'>"  .  __('Edit', 'ff_newsletter')  .  "</a>";
		echo "<a class='view button button-secondary button-small' href='" . get_the_permalink($child->ID) . "' target='_blank'>"  .  __('View', 'ff_newsletter')  .  "</a>";
		echo "<a class='delete button button-secondary button-small' href='" .  get_delete_post_link($child->ID) . "'>"  .  __('Trash', 'ff_newsletter')  .  "</a>";
		echo "</div>";

		// closing wrapper tag
		printf('</%s>', $wrapper);
	}
}
