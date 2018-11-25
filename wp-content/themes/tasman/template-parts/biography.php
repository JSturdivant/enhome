<?php
/**
 * The template part for displaying an Author biography
 *
 * @package Tasman
 */
?>

<div class="author-info">

	<h2 class="about-author"><?php esc_html_e( 'About The Author', 'tasman' );?></h2>

	<figure class="author-avatar">
		<?php
		/**
		 * Filter the Tasman author bio avatar size.
		 *
		 * @param int $size The avatar height and width size in pixels.
		 */
		$author_bio_avatar_size = apply_filters( 'tasman_author_bio_avatar_size', 96 );

		echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
		?>
	</figure><!-- .author-avatar -->

	<div class="author-detail">

		<h3 class="author-title">
			<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php echo esc_html( get_the_author() ); ?>
			</a>
		</h3>

		<div class="author-description">

			<p class="author-bio">
				<?php wp_kses_post( the_author_meta( 'description' ) ); ?>
			</p><!-- .author-bio -->

		</div><!-- .author-description -->

	</div><!-- .author-detail -->

</div><!-- .author-info -->
