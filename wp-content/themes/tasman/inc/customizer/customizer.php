<?php
/**
 * Tasman Theme Customizer
 *
 * @package Tasman
 */

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function tasman_customize_preview_js() {

	$suffix = tasman_get_min_suffix();

	wp_enqueue_script( 'tasman_customizer', get_template_directory_uri() . "/assets/js/customizer$suffix.js", array( 'customize-preview', 'customize-selective-refresh' ), '20151215', true );

	$css_selector = tasman_css_color_selector();
	$output = array(
		'primary_color_background' 			=> $css_selector['primary_color_background'],
		'primary_color_border' 				=> $css_selector['primary_color_border'],
		'primary_color_text' 				=> $css_selector['primary_color_text'],
		'secondary_color_background' 		=> $css_selector['secondary_color_background'],
		'secondary_color_text' 				=> $css_selector['secondary_color_text'],
	);
	wp_localize_script( 'tasman_customizer', 'TasmanCustomizerl10n', $output );

}
add_action( 'customize_preview_init', 'tasman_customize_preview_js' );

/**
 * [tasman_setting_default description]
 * @return [type] [description]
 */
function tasman_setting_default(){
	$settings = array(
		'primary_color'		=> '#ff5722',
		'secondary_color'	=> '#e64a19',
		'post_date'			=> true,
		'post_author'		=> true,
		'post_cat'			=> true,
		'post_tag'			=> true,
		'post_comments'		=> true,
		'author_display'	=> true,
		'excerpt_length'	=> 20,
		'posts_navigation'	=> 'posts_navigation',
		'theme_designer'	=> true,
		'return_to_top'		=> true,
	);

	return apply_filters( 'tasman_setting_default', $settings );
}

/**
 * [tasman_css_color_selector description]
 * @return [type] [description]
 */
function tasman_css_color_selector(){

	$tasman_css_color_selector = array(
		'primary_color_background'	=> '
			button,
			input[type="button"],
			input[type="reset"],
			input[type="submit"],
			.screen-reader-text:focus,
			.comment-body > .reply a:hover,
			.comment-body > .reply a:focus,
			#cancel-comment-reply-link:hover,
			#cancel-comment-reply-link:focus,
			.widget_tag_cloud a:hover,
			.widget_tag_cloud a:focus,
			.instagram-follow-link a:hover,
			.instagram-follow-link a:focus,
			.return-to-top:hover,
			.return-to-top:focus
		',
		'primary_color_border'		=> '
			.comment-body > .reply a:hover,
			.comment-body > .reply a:focus,
			.page-numbers:hover:not(.current),
			.page-numbers:focus:not(.current),
			.widget_tag_cloud a:hover,
			.widget_tag_cloud a:focus,
			.instagram-follow-link a:hover,
			.instagram-follow-link a:focus,
			.return-to-top:hover,
			.return-to-top:focus
		',
		'primary_color_text'		=> '
			a,
			.entry-meta a:hover,
			.entry-meta a:focus,
			.entry-title a:hover,
			.entry-title a:focus,
			.entry-footer a:hover,
			.entry-footer a:focus,
			.author-title a:hover,
			.author-title a:focus,
			.comment-meta a:hover,
			.comment-meta a:focus,
			.social-navigation a:hover,
			.social-navigation a:focus
		',

		'secondary_color_background'	=> '
			button:hover,
			button:active,
			button:focus,
			input[type="button"]:hover,
			input[type="button"]:active,
			input[type="button"]:focus,
			input[type="reset"]:hover,
			input[type="reset"]:active,
			input[type="reset"]:focus,
			input[type="submit"]:hover,
			input[type="submit"]:active,
			input[type="submit"]:focus
		',
		'secondary_color_text'	=> '
			a:hover,
			a:focus
		',

	);


	return apply_filters( 'tasman_css_color_selector', $tasman_css_color_selector );
}

/**
 * Load Customizer Setting.
 */
require get_template_directory() . '/inc/customizer/sanitization-callbacks.php';
require get_template_directory() . '/inc/customizer/settings.php';
require get_template_directory() . '/inc/customizer/output.php';
