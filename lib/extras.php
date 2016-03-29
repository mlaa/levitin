<?php

namespace MLA\Levitin\Extras;

use MLA\Levitin\Config;

/**
 * Add <body> classes
 */
function body_class( $classes ) {
	// Add page slug if it doesn't exist
	if ( is_single() || is_page() && ( ! is_front_page() ) ) {
		if ( ! in_array( basename( get_permalink() ), $classes ) ) {
			$classes[] = basename( get_permalink() );
		}
	}

	// Add class if sidebar is active
	if ( Config\display_sidebar() ) {
		$classes[] = 'has-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
	return ' &hellip; <a href="' . get_permalink() . '">' . __( 'Continued', 'cpwpst' ) . '</a>';
}
add_filter( 'excerpt_more', __NAMESPACE__ . '\\excerpt_more' );

function levitin_add_search_nav_item( $items ) {
	return $items . '<li class="search"><a href="/site-search">' . __('Search') . '</a></li>';
}
add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\\levitin_add_search_nav_item', 10, 2 );
