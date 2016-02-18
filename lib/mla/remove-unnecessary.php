<?php
/**
 * This file contains functions that remove elements that we deem unnecessary.
 * Redundant titles, redundant buttons, etc.
 */

/**
 * Remove redundant email status button in group headings;
 * this is handled by the group tab "Email Options"
 */
remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );

/**
 * Remove forum subscribe link. Users are already subscribed to the forums
 * when they subscribe to the group. Having more fine-grained control over
 * subscriptions is unnecessary and confusing.
 */
function mla_remove_forum_subscribe_link( $link ){
	return ''; //making this empty so that it will get rid of the forum subscribe link
}
add_filter( 'bbp_get_forum_subscribe_link', 'mla_remove_forum_subscribe_link' );

/**
 * Remove forum title, since in our use cases forum titles have the same names as
 * their parent groups, and users see a redundant title on group forums pages.
 */
function mla_remove_forum_title( $title ) {
}
add_filter( 'bbp_get_forum_title', 'mla_remove_forum_title' );

/**
 * Remove "What's new in ___, Jonathan?"
 * Taken from BP-Group-Announcements.
 */
// Disable the activity update form on the group home page. Props r-a-y
add_action( 'bp_before_group_activity_post_form', create_function( '', 'ob_start();' ), 9999 );
add_action( 'bp_after_group_activity_post_form', create_function( '', 'ob_end_clean();' ), 0 );

/**
 * Hide settings page (we don't want users changing their
 * e-mail or password).
 */
function change_settings_subnav() {
	$args = array(
		'parent_slug' => 'settings',
		'screen_function' => 'bp_core_screen_notification_settings',
		'subnav_slug' => 'notifications',
	);
	bp_core_new_nav_default( $args );
}
add_action( 'bp_setup_nav', 'change_settings_subnav', 5 );

function remove_general_subnav() {
	global $bp;
	bp_core_remove_subnav_item( $bp->settings->slug, 'general' );
}
add_action( 'wp', 'remove_general_subnav', 2 );

/**
 * Remove portfolio subnav area from member activity area settings tab.
 * This page just had lots of visibility settings for CACAP profile areas,
 * but they weren't working properly, and didn't include "free entry" areas.
 */
function mla_remove_portfolio_subnav() {
	global $bp;
	bp_core_remove_subnav_item( $bp->settings->slug, 'profile' );
}
add_action( 'wp', 'mla_remove_portfolio_subnav', 2 );

/**
 * Remove misbehaving forums tab on profile pages.
 */
function remove_forums_nav() {
	bp_core_remove_nav_item( 'forums' );
}
add_action( 'wp', 'remove_forums_nav', 3 );

// remove default profile link handling so we can override it below
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data' );

/**
 * Stop Invite Anyone from adding a nav item to user profiles.
 * This effectively prevents users from being able to invite non-members
 * by email, and simplifies invites overall.
 * See #141 for details.
 */
remove_action( 'bp_setup_nav', 'invite_anyone_setup_nav' );

// Remove redundant subscription button from group header.
remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );

// remove profile progression indicator
if ( class_exists( 'BP_Profile_Progression' ) ) {
	remove_action( 'bp_before_member_header_meta', array( BP_Profile_Progression::instance(), 'member_display' ) );
}

/**
 * remove the "send public message" button.
 */
function levitin_remove_send_public_message_button() {
	remove_action( 'bp_member_header_actions', 'bp_send_public_message_button', 20 );
}
add_action( 'wp', 'levitin_remove_send_public_message_button' ); // 'wp' ensures BuddyPress::setup_actions() has run

/**
 * turn options nav li elements into buttons
 */
function levitin_convert_options_nav_items_to_buttons( $li ) {
	// convert <li> to <div>
	$div = preg_replace( '#<li(.*)li>#', '<div$1div>', $li );

	$doc = new DOMDocument;

	// encoding prevents mangling of multibyte characters
	// constants ensure no <body> or <doctype> tags are added
	$doc->loadHTML( mb_convert_encoding( $div, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

	$doc_div = $doc->getElementsByTagName( 'div' )[0];

	$doc_div->setAttribute( 'class', $doc_div->getAttribute( 'class' ) . ' generic-button' );

	return $doc->saveHTML();
}
foreach ( array( 'edit', 'change-avatar' ) as $a_id ) {
	add_filter( 'bp_get_options_nav_' . $a_id, 'levitin_convert_options_nav_items_to_buttons' );
}

// disable some options nav items
foreach ( array( 'change-cover-image' ) as $a_id ) {
	add_filter( 'bp_get_options_nav_' . $a_id, '__return_false' );
}
