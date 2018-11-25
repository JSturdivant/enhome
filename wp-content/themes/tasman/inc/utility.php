<?php
/**
 * Helper functions
 *
 * @package Tasman
 */

if ( ! function_exists( 'tasman_is_sticky' ) ) :
/**
 * [tasman_is_sticky description]
 * @return bool
 */
function tasman_is_sticky(){
	return (bool) is_sticky() && !is_paged() && !is_singular() && !is_archive();
}
endif;

if( ! function_exists( 'tasman_get_min_suffix' ) ) :
/**
 * Helper function for getting the script/style `.min` suffix for minified files.
 *
 * @return string
 */
function tasman_get_min_suffix() {
	return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
}
endif;

if ( ! function_exists( 'tasman_get_link_url' ) ) :
/**
 * Return the first link found in the post content or fall back to permalink.
 */
function tasman_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;
