<?php
/**
 * This file handle widget appearance behavior.
 *
 * @package Arctic Black
 */

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function tasman_widget_tag_cloud_args( $args ) {
	$args['largest'] = 0.875;
	$args['smallest'] = 0.875;
	$args['unit'] = 'rem';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'tasman_widget_tag_cloud_args' );

/**
 * [tasman_instagram_text_link description]
 * @param  [type] $class [description]
 * @return [type]        [description]
 */
function tasman_instagram_text_link( $class ){

	$class = 'instagram-follow-link';
	return $class;

}
add_filter( 'wpiw_link_class', 'tasman_instagram_text_link' );

/**
 * Wrap count number within a span.
 *
 * @param  [type] $links [description]
 * @return [type]        [description]
 */
function tasman_widget_span_count( $links ) {
	$links = str_replace( '&nbsp;', ' ', $links );
    $links = str_replace( '</a> (', '</a> <span class="count">', $links);
    $links = str_replace( ')', '</span>', $links );
    return $links;
}
add_filter( 'get_archives_link', 'tasman_widget_span_count' );
add_filter( 'wp_list_categories', 'tasman_widget_span_count' );
