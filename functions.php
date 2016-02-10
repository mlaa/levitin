<?php

// Turn down error reporting, specifically to ignore Infinity-generated warnings.
//ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * Levitin includes
 *
 * The $cpwpst_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 */
$cpwpst_includes = [
	'lib/assets.php',                  // Scripts and stylesheets
	'lib/conditional-tag-check.php',   // ConditionalTagCheck class
	'lib/config.php',                  // Configuration
	'lib/custom.php',                  // Custom functions
	'lib/customizer.php',              // Customizer functions
	'lib/extras.php',                  // Extra functions
	'lib/init.php',                    // Initial theme setup and constants
	'lib/titles.php',                  // Page titles
	'lib/utils.php',                   // Utility functions
	'lib/wrapper.php',                 // Theme wrapper class
	'lib/mla/activities.php',          // Custom behaviors for activities.
	'lib/mla/blog-avatars.php',        // Custom blog avatars
	'lib/mla/bp-ges.php',              // Customizations for BuddyPress Group Email Subscriptions
	'lib/mla/bp-global-search.php',    // Customizations for BuddyPress Global Search
	'lib/mla/committees.php',          // Committee behaviors
	'lib/mla/dashboard.php',           // Our awesome dashboard "My Commons" page.
	'lib/mla/group-members-search.php',// Group members search
	'lib/mla/group-filters.php',       // Filters for MLA groups
	'lib/mla/groupblog.php',           // Customizations for the BP-Groupblog plugin
	'lib/mla/messages-search.php',     // Functions for the messages search
	'lib/mla/nonmembers.php',          // Functions for handling nonmembers
	'lib/mla/portfolios.php',          // Functions to customize CACAP "Portfolios"
	'lib/mla/remove-unnecessary.php',  // Remove stuff
];

foreach ( $cpwpst_includes as $file ) {
	if ( ! $filepath = locate_template( $file ) ) {
		trigger_error( sprintf( __( 'Error locating %s for inclusion', 'cpwpst' ), $file ), E_USER_ERROR );
	}

	require_once $filepath;
}
unset($file, $filepath);

/**
 * for edit view. use like bp_the_profile_field().
 * works inside or outside the fields loop.
 * TODO optimize: find some way to look up fields directly rather than (re)winding the loop every time.
 *
 * @param $field_name
 * @global $profile_template
 */
function levitin_edit_profile_field( $field_name ) {
	global $profile_template;

	$profile_template->rewind_fields(); // reset the loop

	while ( bp_profile_fields() ) {
		bp_the_profile_field();

		if ( bp_get_the_profile_field_name() !== $field_name ) {
			continue;
		}

		$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
		$field_type->edit_field_html();

		do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
		bp_profile_visibility_radio_buttons();

		do_action( 'bp_custom_profile_edit_fields' );
		break; // once we output the field we want, no need to continue looping
	}
}
