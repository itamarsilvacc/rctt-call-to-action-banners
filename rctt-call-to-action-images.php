<?php
/*
Plugin Name: Imagem CTA
Plugin URI: https://itamarsilva.eti.br
Description: Esse plugin possibilita que o usuário adicione banners com link e imagem que possam ser inseridos em qualquer post utilizando shortcodes.
Version: 0.1.1
Author: Itamar Silva
Author URI: https://itamarsilva.eti.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  rctt-call-to-action-images
*/

/**
* Create Images CTA's Post Type
*/
function rctt_image_cta_post_type(){

    /* Register post type for Images CTA's */
	register_post_type('rctt_image_cta', array(
		'labels' => array(
				'name' => 'CTA\'s',
				'singular_name' => 'CTA'
				),
			'menu_icon' => 'dashicons-admin-comments',
			'menu_position' => 20,
			'exclude_from_search' => true,
			'public' => true,
			'has_archive' => false,
			'supports' => array(
				'title',
				'thumbnail'
				),
			'rewrite' => false
	));

    /* Add image size for Images CTA's */
    add_image_size( 'image-cta', 960, 300, true );
}
add_action('init', 'rctt_image_cta_post_type');

/**
* Add custom field URL for Images CTA
*/
function rctt_image_cta_url() {
    global $post;
    $key = 'rctt_image_cta_url_link';

    if ( empty ( $post ) || 'rctt_image_cta' !== get_post_type( $GLOBALS['post'] ) )
        return;

    if ( ! $content = get_post_meta( $post->ID, $key, TRUE ) )
        $content = '';

        printf(
            '<p><label for="%1$s_id"><strong>URL</strong></label><br>
            <input type="url" name="%1$s" id="%1$s_id" value="%2$s" class="all-options" /></p><p>Link de para onde o CTA deve redirecionar o usuário. Informe a url completa, ex: https://blog.com.br/meu-ebook .',
            $key,
            esc_attr( $content )
        );
}
add_action( 'edit_form_after_title', 'rctt_image_cta_url' );

/**
* Save custom field URL for Images CTA
*/
function rctt_image_cta_url_save( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    $key = 'rctt_image_cta_url_link';

    if ( isset ( $_POST[ $key ] ) )
        return update_post_meta( $post_id, $key, $_POST[ $key ] );

    delete_post_meta( $post_id, $key );
}
add_action( 'save_post', 'rctt_image_cta_url_save' );

/**
* Remove permalink from MetaBox Images CTA
*/
function rctt_image_cta_sample_permalink_remove( $return ) {
	global $post;
	if($post->post_type == 'rctt_image_cta'){
	    $return = '';
		return $return;
	}
	else{
		return $return;
	}
}
add_filter( 'get_sample_permalink_html', 'rctt_image_cta_sample_permalink_remove' );

/**
* Create Images CTA Shortcode
*/
function rctt_image_cta_shortcode( $atts ) {
    $image_cta = get_post( $atts['id'] );
    $key = 'rctt_image_cta_url_link';


    if ( ! $content = get_post_meta( $atts['id'], $key, TRUE ) )
        $content = '';

        printf(
            '<div class="rctt-banner-cta">
                <a href="%1$s" title="%2$s">
                    %3$s
                </a>
            </div>',
            esc_attr( $content ),
            $image_cta->post_title,
            get_the_post_thumbnail( $atts['id'], 'image-cta' )
        );
}
add_shortcode('banner-cta', 'rctt_image_cta_shortcode');
