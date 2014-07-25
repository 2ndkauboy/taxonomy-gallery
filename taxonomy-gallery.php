<?php
/*
 * Plugin Name: Taxonomy Gallery
 * Description: Adding the shortcode "taxonomy_gallery" to list all images for a given taxonomy
 * Version: 0.2
 * Author: Bernhard Kau
 * Author URI: http://kau-boys.de
 */

function tgs_add_tags_to_attachments() {
	register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'tgs_add_tags_to_attachments' );

function tgs_taxonomy_gallery_shortcode( $atts ) {

	$gallery = '';

	extract( shortcode_atts( array(
		'category_id' => '',
		'category_name' => '',
		'tag_id' => '',
		'tag_name' => '',
		'size' => 'full',
		'link' => '',
		'class' => '',
	), $atts ) );

	if ( !empty( $category_id ) ) {
		$query = 'cat=' . $category_id;
	} elseif ( !empty( $category_name ) ) {
		$query = 'category_name=' . $category_name;
	} elseif ( !empty( $tag_id ) ) {
		$query = 'tag_id=' . $tag_id;
	} elseif ( !empty( $tag_name ) ) {
		$query = 'tag=' . $tag_name;
	}

	if ( empty( $query ) ) {
		return false;
	}

	$my_query = new WP_Query( $query . '&post_type=attachment&post_status=inherit&posts_per_page=-1' );

	if ( $my_query->have_posts() ) {
		$gallery .= '<div class="taxonomy-gallery">';

		while ( $my_query->have_posts() ) {
			$my_query->the_post();
			$post = get_post();

			$image_attr = array(
				'class'	=> "taxonomy-gallery-img attachment-$size $class",
				'alt' => esc_attr( trim( strip_tags( $post->post_title ) ) )
			);

			$image = wp_get_attachment_image( $post->ID, $size, false, $image_attr );

			if( 'attachment_page' == $link ) {
				$image_link = get_attachment_link( $post->ID );
			} elseif( 'full_image' == $link ) {
				$image_attributes = wp_get_attachment_image_src( $post->ID, 'full' );
				$image_link = $image_attributes[0];
			} else {
				$image_link = '';
			}

			if ( empty( $image_link ) ) {
				$gallery .= $image;
			} else {
				$gallery .= '<a href="' . esc_attr( $image_link ) . '">' . $image . '</a>';
			}
		}

		$gallery .= '</div>';
	}

	return $gallery;
}
add_shortcode( 'taxonomy_gallery', 'tgs_taxonomy_gallery_shortcode' );