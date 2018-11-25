<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Tasman
 */

if ( ! function_exists( 'tasman_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function tasman_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

	$byline = sprintf(
		/* translators: %s: post author. */
		esc_html_x( 'by %s', 'post author', 'tasman' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'tasman_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function tasman_entry_footer() {

	$allowed_html = array(
	    'a' => array(
	        'href' => array(),
	        'title' => array()
	    )
	);

	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'tasman' ) );
		if ( $categories_list ) {
			/* translators: 1: Category Icon, 2: Posted In screen reader, 3: list of categories.  */
			printf( '<span class="cat-links">%1$s <span class="screen-reader-text">%2$s</span> %3$s</span>', tasman_get_svg( array( 'icon' => 'category' ) ), esc_html__( 'Posted in', 'tasman' ), wp_kses( $categories_list, $allowed_html ) ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'tasman' ) );
		if ( $tags_list ) {
			/* translators: 1: Tag Icon, 2: Posted In screen reader, 3: list of tags.  */
			printf( '<span class="tags-links">%1$s <span class="screen-reader-text">%2$s</span> %3$s</span>', tasman_get_svg( array( 'icon' => 'tag' ) ), esc_html__( 'Tagged', 'tasman' ), wp_kses( $tags_list, $allowed_html ) ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		echo tasman_get_svg( array( 'icon' => 'comment' ) ); // WPCS: XSS OK.
		comments_popup_link(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'tasman' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Edit <span class="screen-reader-text">%s</span>', 'tasman' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

if ( !function_exists( 'tasman_posts_navigation' ) ) :
/**
 * [tasman_posts_navigation description]
 * @return [type] [description]
 */
function tasman_posts_navigation(){

	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
		return;
	}

	if ( get_theme_mod( 'posts_navigation', 'posts_navigation' ) == 'posts_navigation' ) {
		the_posts_navigation( array(
            'prev_text'          => esc_html__( '&larr; Older posts', 'tasman' ),
            'next_text'          => esc_html__( 'Newer posts &rarr;', 'tasman' ),
		) );
	} else {
		the_posts_pagination( array(
			'prev_text'          => sprintf( '%s <span class="screen-reader-text">%s</span>', tasman_get_svg( array( 'icon' => 'previous' ) ), esc_html__( 'Previous Page', 'tasman' ) ),
			'next_text'          => sprintf( '%s <span class="screen-reader-text">%s</span>', tasman_get_svg( array( 'icon' => 'next' ) ), esc_html__( 'Next Page', 'tasman' ) ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'tasman' ) . ' </span>',
		) );
	}

}
endif;

if ( ! function_exists( 'tasman_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 */
function tasman_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

if ( ! function_exists( 'tasman_post_thumbnail' ) ) :
/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index
 * views, or a div element when on single views.
 */
function tasman_post_thumbnail( $size = 'post-thumbnail') {

	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( ! is_singular() ) {
		echo '<div class="post-thumbnail">';
			echo '<a href="'. esc_url( get_permalink( get_the_id() ) ) .'">';
				the_post_thumbnail( $size );
			echo '</a>';
		echo '</div>';
	}

}
endif;

if( ! function_exists( 'tasman_footer_copyright' ) ) :
/**
 * [tasman_footer_copyright description]
 * @return [type] [description]
 */
function tasman_get_footer_copyright(){
	// Translators: %1$s: Current year, %2$s: Site link, %3$s: WordPress site link
	$default_footer_copyright =	sprintf( __( 'Copyright &copy; %1$s %2$s. Proudly powered by %3$s.', 'tasman' ),
		date_i18n( __( 'Y', 'tasman' ) ),
		'<a href="'. esc_url( home_url() ) .'">'. esc_html( get_bloginfo( 'name' ) ) .'</a>',
		'<a href="'. esc_url( 'https://wordpress.org/' ) .'">WordPress</a>' );

	apply_filters( 'tasman_footer_copyright', $default_footer_copyright );

	return $default_footer_copyright;

}
endif;

if( ! function_exists( 'tasman_do_footer_copyright' ) ) :
/**
 * [tasman_do_footer_copyright description]
 * @return [type] [description]
 */
function tasman_do_footer_copyright(){

	echo '<div class="site-info">'. wp_kses_post( tasman_get_footer_copyright() ) . '</div>';
	if ( get_theme_mod( 'theme_designer', true ) ) {
		echo '<div class="site-designer">';
		// Translators: %1$s: Theme designer site link
		echo sprintf( esc_html__( 'Theme design by %1$s.', 'tasman' ), '<a href="'. esc_url( 'https://elevate360.com.au/' ) .'" target="_blank" rel="noopener noreferrer">Elevate360</a>' ); // WPCS: XSS OK.
		echo '</div>';
	}

}
endif;

if ( ! function_exists( 'tasman_return_to_top' ) ) :
/**
 * [tasman_return_to_top description]
 * @return string
 */
function tasman_return_to_top(){
	if( get_theme_mod( 'return_top', true ) ) {
		echo '<a href="#page" class="return-to-top">'. tasman_get_svg( array( 'icon' => 'top' ) ) .'</a>'; // WPCS: XSS OK.
	}
}
endif;
