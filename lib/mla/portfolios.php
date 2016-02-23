<?php
/*
 * This file contains custom functions relating to "portfolios,"
 * or profiles. They're mostly customizations for the plugin
 * CAC-Advanced-Profiles.
 */

function mla_remove_name_from_edit_profile($cols) {
	// Assuming "1" is going to be "name."
	// We have to rebuild the array, too.
	$cols['left'] = array_values( array_diff( $cols['left'], array( 1 ) ) );
	return $cols;
}
add_filter('cacap_header_edit_columns', 'mla_remove_name_from_edit_profile');

// remove default profile link handling so we can override it below
remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data' );

// Custom xprofile interest linkifier that accepts semicolons as delimiters.
function mla_xprofile_filter_link_profile_data( $field_value, $field_type = 'textbox' ) {

	if ( 'datebox' === $field_type ) {
		return $field_value;
	}

	if ( ! strpos( $field_value, ',' ) && !strpos( $field_value, '; ' )  && ( count( explode( ' ', $field_value ) ) > 5 ) ) {
		return $field_value;
	}

	if ( strpos( $field_value, '; ' ) ) {
		$list_type = 'semicolon';
		$values = explode( '; ', $field_value ); // semicolon-separated lists
	} else {
		$list_type = 'comma';
		$values = explode( ',', $field_value ); // comma-separated lists
	}

	if ( ! empty( $values ) ) {
		foreach ( (array) $values as $value ) {
			$value = trim( $value );

			// remove <br>s at the ends of interest lists,
			// so that the final search term works
			$value = preg_replace( '/\<br \/\>$/', '', $value );

			// If the value is a URL, skip it and just make it clickable.
			if ( preg_match( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $value ) ) {
				$new_values[] = make_clickable( $value );

			// Is not clickable
			} else {

				// More than 5 spaces
				if ( count( explode( ' ', $value ) ) > 5 ) {
					$new_values[] = $value;

				// Less than 5 spaces
				} else {
					if ( preg_match( '/\.$/', $value ) ) { // if it ends in a period
						$value = preg_replace( '/\.$/', '', $value ); // remove the period at the end
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>.'; // but add it back *after* the link.
					} else if ( preg_match( '/\.\<br \/\>/', $value ) ) {
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>.<br />';
					} else if ( preg_match( '/\<br \/\>/', $value ) ) {
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a><br />';

					} else {
						$search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_members_directory_permalink() );
						$new_values[] = '<a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $value . '</a>';
					}
				}
			}
		}

		if ( 'semicolon' == $list_type ) {
			$values = implode( '; ', $new_values );
		} else {
			$values = implode( ', ', $new_values );
		}
	}

	return $values;
}
add_filter( 'bp_get_the_profile_field_value', 'mla_xprofile_filter_link_profile_data', 9, 2 );

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


if ( class_exists( 'Mla_Academic_Interests' ) ) {
	class Levitin_Mla_Academic_Interests extends Mla_Academic_Interests {
		/**
		 * TODO cleanest way to do this?
		 * we don't need to add all the hooks twice, so do nothing here and don't call the parent constructor
		 */
		public function __construct() {
			add_action( 'xprofile_updated_profile', array( $this, 'levitin_save_user_mla_academic_interests_terms' ) );
		}

		/**
		 * frontend version of edit_user_mla_academic_interests_section() from Mla_Academic_Interests plugin
		 */
		public function levitin_edit_user_mla_academic_interests_section() {

			$tax = get_taxonomy( 'mla_academic_interests' );

			$html = '<span class="description">Enter interests from the existing list, or add new interests if needed.</span><br />';
			$html .= '<select name="academic-interests[]" class="js-basic-multiple-tags interests" multiple="multiple" data-placeholder="Enter interests.">';
			$interest_list = $this->mla_academic_interests_list();
			$input_interest_list = wp_get_object_terms( bp_displayed_user_id(), 'mla_academic_interests', array( 'fields' => 'names' ) );
			foreach ( $interest_list as $interest_key => $interest_value ) {
				$html .= sprintf('			<option class="level-1" %1$s value="%2$s">%3$s</option>' . "\n",
					( in_array( $interest_key, $input_interest_list ) ) ? 'selected="selected"' : '',
					$interest_key,
					$interest_value
				);
			}
			$html .= '</select>';
			echo $html;
		}

		/**
		 * view linked list of interests for displayed user
		 */
		public function view_user_mla_academic_interests_section() {
			$tax = get_taxonomy( 'mla_academic_interests' );

			$interests = wp_get_object_terms( bp_displayed_user_id(), 'mla_academic_interests', array( 'fields' => 'names' ) );

			$html = '<ul>';
			foreach ( $interests as $term_name ) {
				$search_url = add_query_arg( array( 'academic_interests' => urlencode( $term_name ) ), bp_get_members_directory_permalink() );
				$html .= '<li><a href="' . esc_url( $search_url ) . '" rel="nofollow">' . $term_name . '</a></li>';
			}
			$html .= '</ul>';
			echo $html;
		}

		/**
		 * Saves the terms selected on the edit user/profile page on the frontend
		 *
		 * @param int $user_id The ID of the user to save the terms for.
		 */
		public function levitin_save_user_mla_academic_interests_terms( $user_id ) {
			$tax = get_taxonomy( 'mla_academic_interests' );

			// If array add any new keywords.
			if ( is_array( $_POST['academic-interests'] ) ) {
				foreach ( $_POST['academic-interests'] as $term_id ) {
					$term_key = term_exists( $term_id, 'mla_academic_interests' );
					if ( empty( $term_key ) ) {
						$term_key = wp_insert_term( sanitize_text_field( $term_id ), 'mla_academic_interests' );
					}
					if ( ! is_wp_error( $term_key ) ) {
						$term_ids[] = intval( $term_key['term_id'] );
					} else {
						error_log( '*****CAC Academic Interests Error - bad tag*****' . var_export( $term_key, true ) );
					}
				}
			}

			// Set object terms for tags.
			$term_taxonomy_ids = wp_set_object_terms( $user_id, $term_ids, 'mla_academic_interests' );
			clean_object_term_cache( $user_id, 'mla_academic_interests' );

			// Set user meta for theme query.
			delete_user_meta( $user_id, 'academic_interests' );
			foreach ( $term_taxonomy_ids as $term_taxonomy_id ) {
				add_user_meta( $user_id, 'academic_interests', $term_taxonomy_id, $unique = false );
			}

		}
	}

	$levitin_mla_academic_interests = new Levitin_Mla_Academic_Interests;
}
