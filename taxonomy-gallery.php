<?php
/*
 * Plugin Name: Taxonomy Gallery
 * Description: Adding the shortcode "taxonomy_gallery" to list all images for a given taxonomy
 * Version: 1.0.0
 * Author: Bernhard Kau
 * Author URI: http://kau-boys.de
 * Plugin URI: https://github.com/2ndkauboy/taxonomy-gallery
 * Text Domain: taxonomy-gallery
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 */

function tgs_add_tags_to_attachments() {
	register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'tgs_add_tags_to_attachments' );

function tgs_taxonomy_gallery_shortcode( $atts ) {

	$a = shortcode_atts( array(
		'category_id' => '',
		'category_name' => '',
		'tag_id' => '',
		'tag_name' => '',
	), $atts );

	if ( !empty( $a['category_id'] ) ) {
		$query_params = 'cat=' . $a['category_id'];
	} elseif ( !empty( $a['category_name'] ) ) {
		$query_params = 'category_name=' . $a['category_name'];
	} elseif ( !empty( $a['tag_id'] ) ) {
		$query_params = 'tag_id=' . $a['tag_id'];
	} elseif ( !empty( $a['tag_name'] ) ) {
		$query_params = 'tag=' . $a['tag_name'];
	}

	if ( empty( $query_params ) ) {
		return false;
	}

	$taxonomy_query = new WP_Query( $query_params . '&post_type=attachment&post_status=inherit&posts_per_page=-1&fields=ids' );

	if ( $taxonomy_query->have_posts() ) {
		$atts[ 'ids' ] = array();

		foreach ( $taxonomy_query->posts as $post_id ) {
			$atts['ids'][] = $post_id;
		}
	}

	return gallery_shortcode( $atts );
}
add_shortcode( 'taxonomy_gallery', 'tgs_taxonomy_gallery_shortcode' );