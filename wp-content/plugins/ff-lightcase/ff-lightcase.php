<?php

namespace Firefly;

/**
 * Adds Lightcase http://cornel.bopp-art.com/lightcase to linked images and galleries
 *
 * @wordpress-plugin
 * Plugin Name:       Firefly Lightcase
 * Plugin URI:        www.fi.net.au
 * Description:       Adds Lightcase lightbox to images and galleries
 * Version:           1.0.1
 * Author:            Firefly Interactive
 * Author URI:        www.fi.net.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ff-lightcase
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Lightcase {

	protected $version = '1.0.1'; 

	function __construct() {
		add_action('wp_enqueue_scripts', 	[$this, 'enqueueStyle']);
		add_action('wp_enqueue_scripts', 	[$this, 'enqueueScript']);
		add_filter('the_content', 			[$this, 'convertImagesToLightcase']);
		add_filter('post_gallery', 			[$this, 'convertGalleryToLightcase'], 10, 2);
	}	

	public function convertImagesToLightcase($content)
	{
		preg_match_all( '/<a(.*?)href=(?:\'|")([^<]*?).(bmp|gif|jpeg|jpg|png)(?:\'|")(.*?)>/is', $content, $links );

		if(empty($links[0])) return $content;

		foreach ($links[0] as $id => $link) {
			$content = str_replace( $link, '<a' . $links[1][$id] . 'href="' . $links[2][$id] . '.' . $links[3][$id] . '"' . $links[4][$id] . ' data-rel="lightcase">', $content );
		}

		return $content;
	}

	public function enqueueStyle()
	{
		wp_enqueue_style( 'ff-lightcase', plugins_url('public/css/lightcase.css', __FILE__), [], $this->version  );
	}

	public function enqueueScript()
	{
		wp_enqueue_script( 'ff-jquery-touch', plugins_url('public/js/jquery.events.touch.min.js', __FILE__), ['jquery'], $this->version, true );
		wp_enqueue_script( 'ff-lightcase', plugins_url('public/js/lightcase.js', __FILE__), ['jquery', 'ff-jquery-touch'], $this->version, true );
		wp_enqueue_script( 'ff-lightcase-init', plugins_url('public/js/lightcase-init.js', __FILE__), ['jquery', 'ff-lightcase'], $this->version, true );
	}

	public function convertGalleryToLightcase($output, $attr)
	{
		global $post;

        static $instance = 0;
        $instance++;

		$gallery_settings = [
			'thumbnail_size' => 'medium',
			'lightbox_size' => 'full',
		];

		if (isset($attr['orderby'])) {
		    $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		    if (!$attr['orderby'])
		        unset($attr['orderby']);
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );
		$atts = shortcode_atts( array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post ? $post->ID : 0,
            'itemtag'    => $html5 ? 'figure'     : 'dl',
            'icontag'    => $html5 ? 'div'        : 'dt',
            'captiontag' => $html5 ? 'figcaption' : 'dd',
            'columns'    => 3,
            'size'       => 'thumbnail',
            'include'    => '',
            'exclude'    => '',
            'link'       => ''
        ), $attr, 'gallery' );

		$id = intval( $atts['id'] );

    if ( ! empty( $atts['include'] ) ) {
        $_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( ! empty( $atts['exclude'] ) ) {
        $attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
    } else {
        $attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
    }

    if ( empty( $attachments ) ) {
        return '';
    }

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment ) {
            $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
        }
        return $output;
    }

    $itemtag = tag_escape( $atts['itemtag'] );
    $captiontag = tag_escape( $atts['captiontag'] );
    $icontag = tag_escape( $atts['icontag'] );
    $valid_tags = wp_kses_allowed_html( 'post' );
    if ( ! isset( $valid_tags[ $itemtag ] ) ) {
        $itemtag = 'dl';
    }
    if ( ! isset( $valid_tags[ $captiontag ] ) ) {
        $captiontag = 'dd';
    }
    if ( ! isset( $valid_tags[ $icontag ] ) ) {
        $icontag = 'dt';
    }

    $columns = intval( $atts['columns'] );
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = '';
    $size_class = $gallery_settings['thumbnail_size'];

		// Here's your actual output, you may customize it to your need
		$output = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>\n";

		// Now you loop through each attachment
		foreach ($attachments as $id => $attachment) {
		    // Fetch all data related to attachment 
		    $img = wp_prepare_attachment_for_js($id);

		    // If you want a different size change 'large' to eg. 'medium'
		    $url = $img['sizes'][$size_class]['url'];
		    $url_lightcase = $img['sizes'][$gallery_settings['lightbox_size']]['url'];
		    $alt = $img['alt'];

		    // Store the caption
		    $caption = $img['caption'];

		    $output .= "<figure class='gallery-item'>\n";
		    $output .= "<div class='gallery-icon'>\n";
		    $output .= "<a href=\"{$url_lightcase}\" data-rel=\"lightcase:{$instance}:slideshow\" title=\"{$caption}\">\n";
		    $output .= "<img src=\"{$url}\" alt=\"{$alt}\" />\n";
		    $output .= "</a>";

            $output .= "</div>\n";
		    // Output the caption if it exists
		    if ($caption) { 
		        $output .= "<figcaption class='wp-caption-text gallery-caption'>{$caption}</figcaption>\n";
		    }
            $output .= "<div class='gallery-item__overlay'></div>";
		    $output .= "</figure>\n";
		}

		$output .= "</div>\n";

		return $output;
	}
}

$ff_lightcase = new \Firefly\Lightcase;