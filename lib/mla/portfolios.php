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


// TODO should this live in its own plugin?
class Levitin_Mla_Academic_Interests extends Mla_Academic_Interests {
	/**
	 * frontend version of edit_user_mla_academic_interests_section() from Mla_Academic_Interests plugin
	 *
	 * @param object $user The user object currently being edited.
	 */
	public function edit_user_mla_academic_interests_section( $user ) {

		$tax = get_taxonomy( 'mla_academic_interests' );

		/* Make sure the user can assign terms of the mla_academic_interests taxonomy before proceeding. */
		if ( ! current_user_can( $tax->cap->assign_terms ) ) {
			return;
		}

		$html = '<span class="description">Enter interests from the existing list, or add new interests if needed.</span><br />';
		$html .= '<select name="academic-interests[]" class="js-basic-multiple-tags interests" multiple="multiple" data-placeholder="Enter interests.">';
		$interest_list = $this->mla_academic_interests_list();
		$input_interest_list = wp_get_object_terms( $user->ID, 'mla_academic_interests', array( 'fields' => 'names' ) );
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
}

$levitin_mla_academic_interests = new Levitin_Mla_Academic_Interests;
