<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tasman
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
	<div class="wrap">
		<?php if( is_active_sidebar( 'sidebar-1' ) ) :?>
			<div id="footer-widgets" class="footer-widgets row">
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			</div>
		<?php endif;?>
	</div><!-- .wrap -->
</aside><!-- #secondary -->
