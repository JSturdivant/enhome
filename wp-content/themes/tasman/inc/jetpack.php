<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.com/
 *
 * @package Tasman
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 */
function tasman_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'type'      		=> 'click',
		'container' 		=> 'main',
		'render'    		=> 'tasman_infinite_scroll_render',
		'footer_widgets'	=> array( 'sidebar-1' ),
	) );
}
add_action( 'after_setup_theme', 'tasman_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function tasman_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
			get_template_part( 'template-parts/content', 'search' );
		else :
			get_template_part( 'template-parts/content', get_post_format() );
		endif;
	}
}

/** Remove Jetpack Infinity Scroll CSS */
function tasman_deregister_jetpack_styles(){
	wp_deregister_style( 'the-neverending-homepage' );
}
add_action( 'wp_print_styles', 'tasman_deregister_jetpack_styles' );
