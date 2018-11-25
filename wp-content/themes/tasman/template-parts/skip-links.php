<?php
/**
 * Skip links
 *
 * @package Tasman
 */
?>

<?php if( has_nav_menu( 'menu-1' ) ) :?>
	<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_html_e( 'Skip to navigation', 'tasman' ); ?></a>
<?php endif;?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'tasman' ); ?></a>

<?php if( is_active_sidebar( 'sidebar-1' ) ) :?>
	<a class="skip-link screen-reader-text" href="#secondary"><?php esc_html_e( 'Skip to Footer', 'tasman' ); ?></a>
<?php endif;?>
