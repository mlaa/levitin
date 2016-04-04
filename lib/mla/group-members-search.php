<?php
/**
 * Output the Group members template.
 * Based on bp_groups_members_template_part(), but simplified.
 *
 * @since BuddyPress (?)
 *
 * @return string html output
 */
function mla_bp_groups_members_template_part() {
	?>

	<?php bp_directory_members_search_form(); ?>

	<div id="members-group-list" class="group_members dir-list">

		<?php bp_get_template_part( 'groups/single/members' ); ?>

	</div>
	<?php
}

// echoes all member group ids in a string form, usable as markup classes (etc.)
// e.g. "group-33 group-150"
// depends on bp_get_member_user_id(), so use only from within the members loop
function levitin_member_group_id_classes() {
	$groups_str = '';

	$groups = BP_Groups_Member::get_group_ids( bp_get_member_user_id() ); // contains a superfluous wrapping array

	foreach ( $groups['groups'] as $group_id ) {
		$groups_str .= "group-$group_id ";
	}

	echo trim( $groups_str );
}
