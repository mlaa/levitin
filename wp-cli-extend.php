<?php

if( class_exists( 'LevitinMigration' ) ) {
	  WP_CLI::add_command( 'migr8', 'LevitinMigration' );
}

class LevitinMigration extends WP_CLI_Command {

	function __construct( $args = array(), $vars = array() ) {
		parent::__construct( $args, $assoc_args );
	}

	// TODO
	public static function help() {
		WP_CLI::line( 'usage: wp migr8' );
	}

	/**
	 * extract variously-formatted data from an xprofile field and try to parse out a taxonomy of terms
	 */
	function interests() {
		global $wpdb;

		// if more than this many spaces are found in a possible interest, assume it's malformed and discard it
		$max_spaces_allowed_per_interest = 5;

		$academic_interests_xprofile_field_id = 9;

		$rows = $wpdb->get_col( $wpdb->prepare(
			'
			SELECT value
			FROM wp_bp_xprofile_data
			WHERE field_id = %s
			',
			$academic_interests_xprofile_field_id
		) );

		$all_interests = [];

		foreach ( $rows as $i => $value ) {
			// sanitize
			$value = strip_tags( html_entity_decode( $value ) );

			// some users use "." for separators; some use ","
			if ( strpos( $value, ',' ) === false && strpos( $value, '.' ) !== false ) {
				$possible_interests = explode( '.', $value );
			} else {
				$possible_interests = explode( ',', $value );
			}

			// check each possible interest
			foreach ( $possible_interests as $possible_interest ) {
				// trim unnecessary characters
				$possible_interest = trim( stripslashes( $possible_interest ) );
				$possible_interest = preg_replace( '/\.$/', '', $possible_interest );
				$possible_interest = preg_replace( '/["\'•	]*/', '', $possible_interest );

				// skip some special cases
				if (
					empty( $possible_interest ) ||
					is_numeric( $possible_interest ) ||
					( strpos( $possible_interest, '('  ) !== false && strpos( $possible_interest, ')' ) === false ) ||
					( strpos( $possible_interest, ')'  ) !== false && strpos( $possible_interest, '(' ) === false ) ||
					( strpos( $possible_interest, '['  ) !== false && strpos( $possible_interest, ']' ) === false ) ||
					( strpos( $possible_interest, ']'  ) !== false && strpos( $possible_interest, '[' ) === false ) ||
					preg_match( '/http(s)?:\/\//', $possible_interest ) === 1 ||
					$possible_interest === 'etc'
				) {
					continue;
				}

				// is this an actual interest (and not part of a sentence etc.)?
				if ( substr_count( $possible_interest, ' ' ) > $max_spaces_allowed_per_interest ) {
					continue; // TODO just skip for now; try to dig deeper later
				}

				// increment counter if this interest has previously been added (case-insensitive)
				if ( in_array( strtolower( $possible_interest ), array_map( 'strtolower', array_keys( $all_interests ) ) ) ) {
					$all_interests[ strtolower( $possible_interest ) ] += 1;
					continue;
				}

				// at this point, assume the term is legitimate & unique and add it
				$all_interests[ strtolower( $possible_interest ) ] = 1;
			}

			//if ( $i > 200 ) break;
		}

		arsort( $all_interests );

		//WP_CLI::line( print_r( $all_interests, true ) );

		//$csv = implode( ',', array_keys( $all_interests ) );

		foreach ( $all_interests as $interest => $count ) {
			WP_CLI::line( "$interest, $count" );
		}
	}
}
