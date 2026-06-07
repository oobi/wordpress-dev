<?php
class Buzz_Addon_Print_View {

	// the rewrite rule used to access the email template
	private $rewrite_rule = 'newsletter/([^/]+)/print/?.*?$';

	public function plugin_init() {

		// add Wordpress hooks
		add_action( 'init', 			array( $this, 'add_rewrite_rules' ), 	10 );
		add_action( 'query_vars', 		array( $this, 'add_url_vars' ), 		10 );
		add_filter( 'template_include', array( $this, 'change_template'), 		10 );

		// add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action('save_post', array( $this, 'save_meta_box' ) );
	}

	/**
	 * The code that runs during plugin activation.
	 */
	public function activate() {

		// make sure the rewrite rules have been added
		$this->add_rewrite_rules();

		// flush rules
		flush_rewrite_rules();
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public function deactivate() {
		global $wp_rewrite;

		// make sure the rewrite rules have been removed
   		unset($wp_rewrite->extra_rules_top[$this->rewrite_rule]);

		// flush rules
		flush_rewrite_rules();
	}

	/**************************************************************************
	 * SET UP PRINT META BOX
	 **************************************************************************/

	/**
	 * Add meta box
	 */
	public function add_meta_box() {
		add_meta_box(
			'buzz_print_view',
			__( 'Print Options', 'buzz-print-view' ),
			[ $this, 'print_view_meta_box' ],
			['article'],
			'side',
			'default'
		);
	}

	/**
	 * Print view meta box
	 */
	public function print_view_meta_box( $post ) {
		$break_before = get_post_meta( $post->ID, '_buzz_print_view_break_before', true );
		$break_before = $break_before ? $break_before : 'no';
		$break_before = $break_before == 'yes' ? 'checked' : '';

		$break_after = get_post_meta( $post->ID, '_buzz_print_view_break_after', true );
		$break_after = $break_after ? $break_after : 'yes';
		$break_after = $break_after == 'yes' ? 'checked' : '';

		$suppress_feature = get_post_meta( $post->ID, '_buzz_print_view_suppress_feature', true );
		$suppress_feature = $suppress_feature ? $suppress_feature : 'no';
		$suppress_feature = $suppress_feature == 'yes' ? 'checked' : '';

		?>
		<p>
			<label for="buzz_print_view_break_before">
				<input type="checkbox" name="buzz_print_view_break_before" id="buzz_print_view_break_before" value="yes" <?php echo $break_before; ?> />
				<?php _e( 'Page Break Before', 'buzz-print-view' ); ?>
			</label>
		</p>
		<p>
			<label for="buzz_print_view_break_after">
				<input type="checkbox" name="buzz_print_view_break_after" id="buzz_print_view_break_after" value="yes" <?php echo $break_after; ?> />
				<?php _e( 'Page Break After', 'buzz-print-view' ); ?>
			</label>
		</p>
		<p>
			<label for="buzz_print_view_suppress_feature">
				<input type="checkbox" name="buzz_print_view_suppress_feature" id="buzz_print_view_suppress_feature" value="yes" <?php echo $suppress_feature; ?> />
				<?php _e( 'Suppress Feature Image', 'buzz-print-view' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['buzz_print_view_break_before'] ) ) {
			update_post_meta( $post_id, '_buzz_print_view_break_before', $_POST['buzz_print_view_break_before'] );
		} else {
			delete_post_meta( $post_id, '_buzz_print_view_break_before' );
		}

		if ( isset( $_POST['buzz_print_view_break_after'] ) ) {
			update_post_meta( $post_id, '_buzz_print_view_break_after', $_POST['buzz_print_view_break_after'] );
		} else {
			delete_post_meta( $post_id, '_buzz_print_view_break_after' );
			update_post_meta( $post_id, '_buzz_print_view_break_after', 'no' );
		}

		if ( isset( $_POST['buzz_print_view_suppress_feature'] ) ) {
			update_post_meta( $post_id, '_buzz_print_view_suppress_feature', $_POST['buzz_print_view_suppress_feature'] );
		} else {
			delete_post_meta( $post_id, '_buzz_print_view_suppress_feature' );
		}
	}


	/**************************************************************************
	 * SET UP PRINT VIEW FUNCTIONALITY
	 **************************************************************************/

	/**
	 * Add custom query vars
	 */
	public function add_url_vars($public_query_vars) {
		array_push($public_query_vars, 'print' );
	    return $public_query_vars;
	}

	/**
	 * Add rewrite rules to redirect query vars to email view
	 */
	public function add_rewrite_rules() {
		add_rewrite_tag('%print%','([^&]+)');
		add_rewrite_rule('newsletter/([^/]+)/print/?.*?$', 'index.php?newsletter=$matches[1]&print=1','top');
	}

	/**
	 * Change to print template if URL variable is present in request
	 */
	public function change_template($template) {
		global $wp_query;
		$filename = basename($template, '.php');

		// if the print flag is set, then add '-print' suffix to the chosen template if it exists
		if(isset($wp_query->query_vars['print'])) {
			$templates = apply_filters('buzz_print_template', array($filename . '-print.php', $template, 'index.php') );
			$new_template = locate_template( $templates );
			if($new_template != '') {
				return $new_template;
			}
		}

		// not found - return original template
		return $template;
	}


	/**************************************************************************
	 * STATIC METHODS
	 **************************************************************************/


	/**
	 * Get print classes
	 */
	public static function get_print_classes( $id ) {
		$classes = array();

		$break_before = get_post_meta( $id, '_buzz_print_view_break_before', true );
		$break_before = $break_before ? $break_before : 'no';
		$break_before = $break_before == 'yes' ? 'page-break-before' : '';
		$classes[] = $break_before;

		$break_after = get_post_meta( $id, '_buzz_print_view_break_after', true );
		$break_after = $break_after ? $break_after : 'yes';
		$break_after = $break_after == 'yes' ? 'page-break-after' : '';
		$classes[] = $break_after;

		return implode(' ', $classes);
	}

	/**
     * get the newsletter print URL
     */
    public static function get_print_url( $newsletter_id ) {
        $newsletter_status 	= get_post_status( $newsletter_id );
        $url_param 			= 'print';

        if( $newsletter_status == 'draft' ) {
            $url_param = '&print=1';
        }

        return $newsletter_id ? get_permalink($newsletter_id) . $url_param : NULL;
    }

	/**
     * Determine if print view
     */
    public static function is_print_view() {
        global $wp_query, $post;
        $is_newsletter = ( $post && FF_Newsletter_Common::is_newsletter_view($post) );
        return ($is_newsletter && !is_admin() && isset($wp_query->query_vars['print']));
    }
}