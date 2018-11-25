<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tasman
 */

?>

	</div><!-- #content -->

	<?php get_sidebar();?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="wrap">
			<?php if ( has_nav_menu( 'menu-3' ) ) : ?>
				<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Social Links Menu', 'tasman' ); ?>">
					<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-3',
							'menu_class'     => 'social-links-menu',
							'depth'          => 1,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>' . tasman_get_svg( array( 'icon' => 'share' ) ),
						) );
					?>
				</nav><!-- .social-navigation -->
			<?php endif;?>
			<?php if( has_nav_menu( 'menu-2' ) ) : ?>
				<nav class="secondary-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Navigation', 'tasman' ); ?>">
					<?php
						wp_nav_menu( array(
							'theme_location' 	=> 'menu-2',
							'menu_id'        	=> 'secondary-menu',
							'container_class' 	=> 'wrap',
							'depth'          	=> 1
						) );
					?>
				</nav><!-- .social-navigation -->
			<?php endif;?>
			<?php tasman_do_footer_copyright();?>
		</div><!-- .wrap -->
	</footer><!-- #colophon -->

</div><!-- #page -->

<?php tasman_return_to_top();?>

<?php wp_footer(); ?>

</body>
</html>
