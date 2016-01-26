<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Avatar  Name               N items in CORE
 *         Title              N groups
 *         Place of Work      N sites
 *         twitter            Following N members
 *         www.site.com       Follow button
 *         social media icons
 */

$twitter_handle = str_replace( '@', '', bp_get_member_profile_data( 'field=<em>Twitter</em> user name' ) ); // remove '@' if necessary
$follow_counts = bp_follow_total_follow_counts();

?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since BuddyPress (1.2.0)
 */
do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">
		<?php bp_displayed_user_avatar( 'type=full' ); ?>
	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<div id="item-main">
		<h4 class="name">
			<?php bp_member_profile_data( 'field=Name' ) ?>
		</h4>
		<h4 class="title">
			<?php bp_member_profile_data( 'field=Title' ) ?>
		</h4>
		<h4 class="affiliation">
			<?php bp_member_profile_data( 'field=Institutional or Other Affiliation' ) ?>
		</h4>
		<div class="username">
			<?php echo "@" . bp_get_displayed_user_username() ?>
		</div>
		<?php if ( ! empty( bp_get_member_profile_data( 'field=Site' ) ) ): ?>
		<div class="site">
			<?php bp_member_profile_data( 'field=Site' ) ?>
		</div>
		<?php endif ?>
	</div><!-- #item-main -->

	<?php

	/**
	 * Fires before the display of the member's header meta.
	 *
	 * @since BuddyPress (1.2.0)
	 */
	do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<div class="n-items-in-core">
			<?php humcore_deposit_count() ?> items in CORE
		</div>
		<div class="n-groups">
			<?php bp_total_group_count_for_user() ?> groups
		</div>
		<div class="n-sites">
			<?php bp_total_blog_count_for_user() ?> sites
		</div>
		<div class="following-n-members">
			Following <?php echo $follow_counts['following'] ?> members
		</div>

		<div id="item-buttons">

			<?php

			/**
			 * Fires in the member header actions section.
			 *
			 * @since BuddyPress (1.2.6)
			 */
			do_action( 'bp_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php

		 /**
		  * Fires after the group header actions section.
		  *
		  * If you'd like to show specific profile fields here use:
		  * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		  *
		  * @since BuddyPress (1.2.0)
		  */
		 do_action( 'bp_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<div id="item-header-stats">
</div><!-- #item-header-stats -->

<?php

/**
 * Fires after the display of a member's header.
 *
 * @since BuddyPress (1.2.0)
 */
do_action( 'bp_after_member_header' ); ?>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
do_action( 'template_notices' ); ?>
