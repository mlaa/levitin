<?php

// Turn down error reporting, specifically to ignore Infinity-generated warnings.
ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

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
	'lib/mla/oracle-api-sync.php',     // Functions for syncing membership data with the MLA API
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

// Remove redundant subscription button from group header.
remove_action( 'bp_group_header_meta', 'ass_group_subscribe_button' );

// remove profile progression indicator until it has a place
remove_action( 'bp_before_member_header_meta', array( BP_Profile_Progression::instance(), 'member_display' ) );

/**
 * ripped from BP_Blogs_Blog::get(), so we can add a filter to handle MPO options:
 *
 * else if ( is_user_logged_in() )
 * 	$hidden_sql = "AND wb.public = -1";
 *
 * if it becomes possible to manipulate the sql that function uses with a parameter or global, we should do that instead
 *
 * @param array $return_value what BP_Blogs_Blog::get() returned. will be entirely replaced by this filter
 * @param array $args the args originally passed to BP_Blogs_Blog::get(), so we can reconstruct the query
 */

function more_privacy_options_blogs_get( $return_value, $args ) {
	global $wpdb;

	extract( $args );

	$bp = buddypress();

	if ( is_user_logged_in() ) {
		$hidden_sql = "AND wb.public in (1, -1)";
	} else {
		if ( !is_user_logged_in() || !bp_current_user_can( 'bp_moderate' ) && ( $user_id != bp_loggedin_user_id() ) )
			$hidden_sql = "AND wb.public = 1";
		else
			$hidden_sql = '';
	}

	$pag_sql = ( $limit && $page ) ? $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) ) : '';

	$user_sql = !empty( $user_id ) ? $wpdb->prepare( " AND b.user_id = %d", $user_id ) : '';

	switch ( $type ) {
		case 'active': default:
			$order_sql = "ORDER BY bm.meta_value DESC";
			break;
		case 'alphabetical':
			$order_sql = "ORDER BY bm_name.meta_value ASC";
			break;
		case 'newest':
			$order_sql = "ORDER BY wb.registered DESC";
			break;
		case 'random':
			$order_sql = "ORDER BY RAND()";
			break;
	}

	$include_sql = '';
	$include_blog_ids = array_filter( wp_parse_id_list( $include_blog_ids ) );
	if ( ! empty( $include_blog_ids ) ) {
		$blog_ids_sql = implode( ',', $include_blog_ids );
		$include_sql  = " AND b.blog_id IN ({$blog_ids_sql})";
	}

	if ( ! empty( $search_terms ) ) {
		$search_terms_like = '%' . bp_esc_like( $search_terms ) . '%';
		$search_terms_sql  = $wpdb->prepare( 'AND (bm_name.meta_value LIKE %s OR bm_description.meta_value LIKE %s)', $search_terms_like, $search_terms_like );
	} else {
		$search_terms_sql = '';
	}

	$paged_blogs = $wpdb->get_results( "
		SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm_name.meta_value as name
		FROM
		  {$bp->blogs->table_name} b
		  LEFT JOIN {$bp->blogs->table_name_blogmeta} bm ON (b.blog_id = bm.blog_id)
		  LEFT JOIN {$bp->blogs->table_name_blogmeta} bm_name ON (b.blog_id = bm_name.blog_id)
		  LEFT JOIN {$bp->blogs->table_name_blogmeta} bm_description ON (b.blog_id = bm_description.blog_id)
		  LEFT JOIN {$wpdb->base_prefix}blogs wb ON (b.blog_id = wb.blog_id)
		  LEFT JOIN {$wpdb->users} u ON (b.user_id = u.ID)
		WHERE
		  wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql}
		  AND bm.meta_key = 'last_activity' AND bm_name.meta_key = 'name' AND bm_description.meta_key = 'description'
		  {$search_terms_sql} {$user_sql} {$include_sql}
		GROUP BY b.blog_id {$order_sql} {$pag_sql}
	" );

	$total_blogs = $wpdb->get_var( "
		SELECT COUNT(DISTINCT b.blog_id)
		FROM
		  {$bp->blogs->table_name} b
		  LEFT JOIN {$wpdb->base_prefix}blogs wb ON (b.blog_id = wb.blog_id)
		  LEFT JOIN {$bp->blogs->table_name_blogmeta} bm_name ON (b.blog_id = bm_name.blog_id)
		  LEFT JOIN {$bp->blogs->table_name_blogmeta} bm_description ON (b.blog_id = bm_description.blog_id)
		WHERE
		  wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 {$hidden_sql}
		  AND
		  bm_name.meta_key = 'name' AND bm_description.meta_key = 'description'
		  {$search_terms_sql} {$user_sql} {$include_sql}
	" );

	$blog_ids = array();
	foreach ( (array) $paged_blogs as $blog ) {
		$blog_ids[] = (int) $blog->blog_id;
	}

	$paged_blogs = BP_Blogs_Blog::get_blog_extras( $paged_blogs, $blog_ids, $type );

	if ( $update_meta_cache ) {
		bp_blogs_update_meta_cache( $blog_ids );
	}

	return array( 'blogs' => $paged_blogs, 'total' => $total_blogs );
}
add_filter( 'bp_blogs_get_blogs', 'more_privacy_options_blogs_get', null, 3 );
