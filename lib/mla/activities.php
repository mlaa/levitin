<?php
/**
 * Filter membership activities out of AJAX querystrings.
 * This effectively makes it so that the "Everything" filter
 * of the Activities page (the default filter that's set when
 * no other filters are set) is actually set to filter out membership
 * activities. This way, the activity list doesn't overflow with
 * activities like "User ___ joined the group ___."
 */
function mla_filter_querystring( $query_string, $object, $object_fitler, $object_score, $object_page, $object_search_terms, $object_extras ) {
	if ( 'activity' == $object ) {
		// let's just filter when we're on the activity page
		if ( ( strpos( $query_string, 'type=' ) === false ) ) { 
			// if there's no type filter, then the type
			// filter is really "everything." In that case,
			// hijack it and make it not-so-everything
			// (i.e. remove membership data)
			$query_string = "type=activity_update,new_blog_post,new_blog_comment,created_group,updated_profile,new_forum_topic,new_forum_post,new_groupblog_post,added_group_document,edited_group_document,bp_doc_created,bp_doc_edited,bp_doc_comment,bbp_topic_create,bbp_reply_create,new_deposit, new_member&action=activity_update,new_blog_post,new_blog_comment,created_group,updated_profile,new_forum_topic,new_forum_post,new_groupblog_post,added_group_document,edited_group_document,bp_doc_created,bp_doc_edited,bp_doc_comment,bbp_topic_create,bbp_reply_create,new_deposit,new_member&scope=friends,groups,mla";
		}
	}
	return $query_string;
}
add_filter( 'bp_legacy_theme_ajax_querystring', 'mla_filter_querystring', 10, 7 );

/**
 * Don't count joining a group as a "recent activity." This
 * makes it so that the "recently active groups" is a little more
 * useful, since it doesn't show just groups that have had membership
 * changes recently.
 *
 * This feature requires Buddypress >= 2.3.0.
 */
remove_action( 'groups_join_group',           'groups_update_last_activity' );
remove_action( 'groups_leave_group',          'groups_update_last_activity' );

/**
 * Don't show secondary avatars in activity items. These are little icons next
 * to group and member names. They clutter up activity items and generally make everything
 * look pretty messy. Get rid of them!
 *
 * Props to @r-a-y for the fix, from this BP support thread:
 * https://buddypress.org/support/topic/how-can-i-disable-secondary-avatars-in-activity-items/#post-240959
 */
function my_remove_secondary_avatars( $bp_legacy ) {
    remove_filter( 'bp_get_activity_action_pre_meta', array( $bp_legacy, 'secondary_avatars' ), 10, 2 );
}
add_action( 'bp_theme_compat_actions', 'my_remove_secondary_avatars' );

/**
 * Output the activity delete link.
 * Now with a fresh icon!
 *
 * @uses bp_get_activity_delete_link()
 */
function mla_bp_activity_delete_link() {
	echo mla_bp_get_activity_delete_link();
}

	/**
	 * Return an iconified activity delete link.
	 *
	 */
	function mla_bp_get_activity_delete_link() {

		$url   = bp_get_activity_delete_url();
		$class = 'delete-activity';

		// Determine if we're on a single activity page, and customize accordingly
		if ( bp_is_activity_component() && is_numeric( bp_current_action() ) ) {
			$class = 'delete-activity-single';
		}

		$link = '<a href="' . esc_url( $url ) . '" class="button item-button bp-secondary-action ' . $class . ' confirm" rel="nofollow">' .'<span class="dashicons dashicons-trash"></span></a>';

		/**
		 * Filters the activity delete link.
		 *
		 * @since BuddyPress (1.1.0)
		 *
		 * @param string $link Activity delete HTML link.
		 */
		return apply_filters( 'bp_get_activity_delete_link', $link );
	}

/**
 * output an abridged action string (relative to what is stored in the activity table)
 * use like bp_activity_action()
 *
 * @uses bp_get_activity_action()
 * @uses bp_get_activity_type()
 * @uses bp_get_displayed_user_fullname()
 */
function levitin_activity_action() {
	$action = trim( strip_tags( bp_get_activity_action( [ 'no_timestamp' => true ] ), '<a>' ) );
	$activity_type = bp_get_activity_type() ;
	$displayed_user_fullname = bp_get_displayed_user_fullname();
	$link_text_char_limit = 30;

	// shorten/change some action descriptions
	switch ( $activity_type ) {
	case 'updated_profile':
		$action = "updated profile"; // default action is "<name>'s profile was updated"
		break;
	}

	// some types end their action strings with ':' - remove it
	$action = preg_replace( '/:$/', '', $action );

	// div wrapper not only serves to contain the action text but also helps DOMDocument traverse the "tree" without breaking it
	$action = "<div class=\"$activity_type\">" . $action . '</div>';

	$action_doc = new DOMDocument;

	// encoding prevents mangling of multibyte characters
	// constants ensure no <body> or <doctype> tags are added
	$action_doc->loadHTML( mb_convert_encoding( $action, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

	// for reasons yet unknown, removeChild() causes the next anchor to be skipped entirely. using a second foreach is a workaround
	foreach ( $action_doc->getElementsByTagName( 'a' ) as $anchor ) {
		if ( $anchor->nodeValue === $displayed_user_fullname ) {
			$anchor->parentNode->removeChild( $anchor );
			break;
		}
	}
	foreach ( $action_doc->getElementsByTagName( 'a' ) as $anchor ) {
		if ( strlen( $anchor->nodeValue ) > $link_text_char_limit ) {
			$anchor->nodeValue = substr( $anchor->nodeValue, 0, $link_text_char_limit - 1 ) . '…';
		}
	}

	$action = $action_doc->saveHTML();

	echo $action;
}

/**
 * dump of all available bp activity functions and their return values
 */
function activity_debug() {
	echo '<p><pre>';
	var_dump('bp_get_activity_slug()');
	var_dump(bp_get_activity_slug());
	var_dump('bp_get_activity_root_slug()');
	var_dump(bp_get_activity_root_slug());
	var_dump('bp_get_activity_directory_permalink()');
	var_dump(bp_get_activity_directory_permalink());
	var_dump('bp_get_activity_load_more_link()');
	var_dump(bp_get_activity_load_more_link());
	var_dump('bp_get_activity_pagination_count()');
	var_dump(bp_get_activity_pagination_count());
	var_dump('bp_get_activity_pagination_links()');
	var_dump(bp_get_activity_pagination_links());
	var_dump('bp_get_activity_count()');
	var_dump(bp_get_activity_count());
	var_dump('bp_get_activity_per_page()');
	var_dump(bp_get_activity_per_page());
	var_dump('bp_get_activity_id()');
	var_dump(bp_get_activity_id());
	var_dump('bp_get_activity_item_id()');
	var_dump(bp_get_activity_item_id());
	var_dump('bp_get_activity_secondary_item_id()');
	var_dump(bp_get_activity_secondary_item_id());
	var_dump('bp_get_activity_date_recorded()');
	var_dump(bp_get_activity_date_recorded());
	var_dump('bp_get_activity_member_display_name()');
	var_dump(bp_get_activity_member_display_name());
	var_dump('bp_get_activity_object_name()');
	var_dump(bp_get_activity_object_name());
	var_dump('bp_get_activity_type()');
	var_dump(bp_get_activity_type());
	var_dump('bp_get_activity_action_name()');
	var_dump(bp_get_activity_action_name());
	var_dump('bp_get_activity_user_id()');
	var_dump(bp_get_activity_user_id());
	var_dump('bp_get_activity_user_link()');
	var_dump(bp_get_activity_user_link());
	var_dump('bp_get_activity_avatar()');
	var_dump(bp_get_activity_avatar());
	var_dump('bp_get_activity_secondary_avatar()');
	var_dump(bp_get_activity_secondary_avatar());
	var_dump('bp_get_activity_action()');
	var_dump(bp_get_activity_action());
	var_dump('bp_get_activity_content_body()');
	var_dump(bp_get_activity_content_body());
	var_dump('bp_get_activity_content()');
	var_dump(bp_get_activity_content());
	var_dump('bp_get_activity_parent_content()');
	var_dump(bp_get_activity_parent_content());
	var_dump('bp_get_activity_parent_user_id()');
	var_dump(bp_get_activity_parent_user_id());
	var_dump('bp_get_activity_is_favorite()');
	var_dump(bp_get_activity_is_favorite());
	var_dump('bp_get_activity_comment_id()');
	var_dump(bp_get_activity_comment_id());
	var_dump('bp_get_activity_comment_user_id()');
	var_dump(bp_get_activity_comment_user_id());
	var_dump('bp_get_activity_comment_user_link()');
	var_dump(bp_get_activity_comment_user_link());
	var_dump('bp_get_activity_comment_name()');
	var_dump(bp_get_activity_comment_name());
	var_dump('bp_get_activity_comment_date_recorded()');
	var_dump(bp_get_activity_comment_date_recorded());
	var_dump('bp_get_activity_comment_date_recorded_raw()');
	var_dump(bp_get_activity_comment_date_recorded_raw());
	var_dump('bp_get_activity_comment_delete_link()');
	var_dump(bp_get_activity_comment_delete_link());
	var_dump('bp_get_activity_comment_content()');
	var_dump(bp_get_activity_comment_content());
	var_dump('bp_get_activity_comment_link()');
	var_dump(bp_get_activity_comment_link());
	var_dump('bp_get_activity_comment_form_nojs_display()');
	var_dump(bp_get_activity_comment_form_nojs_display());
	var_dump('bp_get_activity_comment_form_action()');
	var_dump(bp_get_activity_comment_form_action());
	var_dump('bp_get_activity_permalink_id()');
	var_dump(bp_get_activity_permalink_id());
	var_dump('bp_get_activity_thread_permalink()');
	var_dump(bp_get_activity_thread_permalink());
	var_dump('bp_get_activity_comment_permalink()');
	var_dump(bp_get_activity_comment_permalink());
	var_dump('bp_get_activity_favorite_link()');
	var_dump(bp_get_activity_favorite_link());
	var_dump('bp_get_activity_unfavorite_link()');
	var_dump(bp_get_activity_unfavorite_link());
	var_dump('bp_get_activity_css_class()');
	var_dump(bp_get_activity_css_class());
	var_dump('bp_get_activity_delete_link()');
	var_dump(bp_get_activity_delete_link());
	var_dump('bp_get_activity_delete_url()');
	var_dump(bp_get_activity_delete_url());
	var_dump('bp_get_activity_latest_update()');
	var_dump(bp_get_activity_latest_update());
	var_dump('bp_get_activity_filter_links()');
	var_dump(bp_get_activity_filter_links());
	var_dump('bp_get_activity_post_form_action()');
	var_dump(bp_get_activity_post_form_action());
	var_dump('bp_get_activity_feed_item_guid()');
	var_dump(bp_get_activity_feed_item_guid());
	var_dump('bp_get_activity_feed_item_title()');
	var_dump(bp_get_activity_feed_item_title());
	var_dump('bp_get_activity_feed_item_link()');
	var_dump(bp_get_activity_feed_item_link());
	var_dump('bp_get_activity_feed_item_date()');
	var_dump(bp_get_activity_feed_item_date());
	var_dump('bp_get_activity_feed_item_description()');
	var_dump(bp_get_activity_feed_item_description());
	var_dump('bp_get_activity_show_filters()');
	var_dump(bp_get_activity_show_filters());
	echo '</pre></p>';
}
