<?php

namespace MLA\Levitin\Config;

use MLA\Levitin\ConditionalTagCheck;

/**
 * Configuration values
 */
if ( ! defined( 'WP_ENV' ) ) {
	// Fallback if WP_ENV isn't defined in your WordPress config
	// Used in lib/assets.php to check for 'development' or 'production'
	define( 'WP_ENV', 'production' );
}

if ( ! defined( 'DIST_DIR' ) ) {
	// Path to the build directory for front-end assets
	define( 'DIST_DIR', '/dist/' );
}

/**
 * Define which pages should have the sidebar
 */
function display_sidebar() {
	static $display;

	if ( ! isset( $display ) ) {
		$conditionalCheck = new ConditionalTagCheck(
			/**
			 * Any of these conditional tags that return true will show the sidebar.
			 * You can also specify your own custom function as long as it returns a boolean.
			 *
			 * To use a function that accepts arguments, use an array instead of just the function name as a string.
			 *
			 * Examples:
			 *
			 * 'is_single'
			 * 'is_archive'
			 * ['is_page', 'about-me']
			 * ['is_tax', ['flavor', 'mild']]
			 * ['is_page_template', 'about.php']
			 * ['is_post_type_archive', ['foo', 'bar', 'baz']]
			 *
			 */
			[
				'is_404',
				['is_page_template', 'template-custom.php']
			]
		);

		$display = apply_filters( 'cpwpst/display_sidebar', $conditionalCheck->result );
	}

	return $display;
}
