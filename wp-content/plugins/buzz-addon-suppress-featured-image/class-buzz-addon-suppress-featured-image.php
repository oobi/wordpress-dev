<?php

class Buzz_Addon_Suppress_Featured_Image {


    /**
    * Init plugin functionality and hooks
    */
    public function plugin_init() {

        // add custom hooks
        add_action( 'buzz_after_article_meta_box', 	array( $this, 'article_meta_box' ), 			10, 2 );
        add_action( 'buzz_after_save_article_meta', array( $this, 'save_article_meta' ), 			10, 2 );
    }

    /**
    * The code that runs during plugin activation.
    */
    public function activate() {

    }

    /**
    * The code that runs during plugin deactivation.
    */
    public function deactivate() {

    }

    /**************************************************************************
    * ARTICLE META BOX
    **************************************************************************/

    /**
    * Add checkbox for email view to the article meta box
    *
    * Extends article_meta_box() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
    */
    public function article_meta_box( $object, $box ) {
        global $pagenow; ?>

		<p>
			<?php
				// get suppression flag
				$suppress_feature = esc_attr( get_post_meta( $object->ID, 'ff_suppress_featured_image', true ) );

				// determine whether checkbox should be checked.
				$is_checked = !empty( $suppress_feature ); ?>

			<input type="checkbox" name="ff_suppress_featured_image" id="ff_suppress_featured_image" value="1" <?php echo $is_checked ? 'checked' : ''; ?>/>
			<label for="ff_suppress_featured_image">
				<?php _e( "Suppress Featured Image", 'ff_newsletter' ); ?>
			</label>
		</p>
		<?php
   }

    /**
    * Save the email checkbox added in article_meta_box().
    *
    * Extends save_article_meta() (in ff-newsletter/admin/ff-newsletter-ui.php) via a hook
    */
    public function save_article_meta( $post_id, $post ) {

        // Get the posted data and sanitize it for use as an HTML class.
        $new_meta_value 	= ( isset( $_POST['ff_suppress_featured_image'] ) 	? intval( $_POST['ff_suppress_featured_image'] ) 	: '' );

        // add/update or delete values accordingly for all settings
        FF_Newsletter_Common::save_meta_values($post_id, 'ff_suppress_featured_image',		$new_meta_value);
    }


}