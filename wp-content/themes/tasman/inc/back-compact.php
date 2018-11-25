<?php
/**
 * Tasman back compat functionality
 *
 * Prevents Tasman from running on WordPress versions prior to 4.7,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.7.
 *
 * @package Tasman
 */

/**
 * Prevent switching to Tasman on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Tasman 1.0.0
 */
function tasman_switch_theme() {
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'tasman_upgrade_notice' );
}
add_action( 'after_switch_theme', 'tasman_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Tasman on WordPress versions prior to 4.7.
 *
 * @since Tasman 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function tasman_upgrade_notice() {
	// Translators: %s: Current WordPress version
	$message = sprintf( __( 'Tasman requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'tasman' ), esc_attr( $GLOBALS['wp_version'] ) );
	printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.7.
 *
 * @since Tasman 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function tasman_customize() {
	// Translators: %s: Current WordPress version
	wp_die( sprintf( esc_html__( 'Tasman requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'tasman' ), esc_attr( $GLOBALS['wp_version'] ) ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'tasman_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.7.
 *
 * @since Tasman 1.0.0
 *
 * @global string $wp_version WordPress version.
 */
function tasman_preview() {
	if ( isset( $_GET['preview'] ) ) {
		// Translators: %s: Current WordPress version
		wp_die( sprintf( esc_html__( 'Tasman requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'tasman' ), esc_attr( $GLOBALS['wp_version'] ) ) );
	}
}
add_action( 'template_redirect', 'tasman_preview' );
