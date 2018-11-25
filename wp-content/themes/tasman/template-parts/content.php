<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Tasman
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">

		<?php if( tasman_is_sticky() ) :?>
			<div class="sticky-label">
				<?php echo tasman_get_svg( array( 'icon' => 'star' ) ); // WPCS: XSS OK.?>
			</div>
		<?php endif;?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php tasman_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif;?>

		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>

		<?php tasman_post_thumbnail();?>

	</header><!-- .entry-header -->

	<?php if( is_singular() || post_password_required() ) : ?>
	<div class="entry-content">
		<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'tasman' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tasman' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php else : ?>
	<div class="entry-summary">
		<?php the_excerpt();?>
	</div><!-- .entry-summary -->
	<?php endif;?>

	<footer class="entry-footer">
		<?php tasman_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
