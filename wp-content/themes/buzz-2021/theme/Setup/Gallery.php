<?php

namespace Firefly\Setup;

use Firefly\Setup\Config;

class Gallery
{

	function __construct()
	{
		//add_filter( 'shortcode_atts_gallery', array( $this, 'shortcode_atts_gallery' ), 10, 3 );
		add_filter( 'post_gallery', [$this, 'post_gallery'], 10, 2 );
	}

	public function shortcode_atts_gallery( $out, $pairs, $atts ) {
	    $atts = shortcode_atts( Config::get('theme')['gallery_attributes'] , $atts );

	    $out['columns'] = $atts['columns'];
	    $out['size'] = $atts['size'];

	    return $out;
	}

	public function post_gallery( $output, $attr )
	{
		global $post;

        static $instance = 0;
        $instance++;

		$gallery_settings = Config::get('theme')['gallery_settings'];

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

	$link = tag_escape( $atts['link'] );
	$has_link = $link !== 'none';
	$link_class = isset( $link ) ? "link-{$link}" : '';

    $selector = "gallery-{$instance}";

    $gallery_style = '';
    $size_class = $gallery_settings['thumbnail_size'] ? $gallery_settings['thumbnail_size'] : tag_escape( $atts['size'] );

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
		    $output .= $has_link ? "<a href=\"{$url_lightcase}\" data-rel=\"lightcase:{$instance}:slideshow\" title=\"{$caption}\">\n" : '';
		    $output .= "<img src=\"{$url}\" alt=\"{$alt}\" />\n";
		    $output .= $has_link ? "</a>" : '';

            $output .= "</div>\n";
		    // Output the caption if it exists
		    if ($caption) {
		        $output .= "<figcaption class='wp-caption-text gallery-caption'>{$caption}</figcaption>\n";
		    }
            $output .= "<div class='gallery-item__overlay d-flex justify-content-center align-items-center'><span class='fal fa-plus fa-2x'></span></div>";
		    $output .= "</figure>\n";
		}

		$output .= "</div>\n";

		return $output;
	}


}

