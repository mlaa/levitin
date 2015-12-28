<div id="buddypress">

	<?php

	/**
	 * Fires before the display of member home content.
	 *
	 * @since BuddyPress (1.2.0)
	 */
	do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php bp_get_template_part( 'members/single/member-header' ) ?>

	</div><!-- #item-header -->

	<div id="item-body">
		<?php
		/**
		 * Fires before the display of member body content.
		 *
		 * @since BuddyPress (1.2.0)
		 */
		do_action( 'bp_before_member_body' ); ?>

		<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
			<ul>
				<?php bp_get_options_nav(); ?>
			</ul>
		</div><!-- .item-list-tabs -->

		<div class="flexbox-container">
			<div>
<?php /* ?>
<?php var_dump(bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action())); ?>
<?php */ ?>
				<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>

					<ul id="activity-stream" class="activity-list item-list">
					<?php while ( bp_activities() ) : bp_the_activity(); ?>

						<?php bp_get_template_part( 'activity/entry' ); ?>

					<?php endwhile; ?>
					</ul>

				<?php endif; ?>
			</div>
			<div>
				<?php

				if ( bp_is_user_activity() || !bp_current_component() ) :
					bp_get_template_part( 'members/single/activity' );

				elseif ( bp_is_user_blogs() ) :
					bp_get_template_part( 'members/single/blogs'    );

				elseif ( bp_is_user_friends() ) :
					bp_get_template_part( 'members/single/friends'  );

				elseif ( bp_is_user_groups() ) :
					bp_get_template_part( 'members/single/groups'   );

				elseif ( bp_is_user_messages() ) :
					bp_get_template_part( 'members/single/messages' );

				elseif ( bp_is_user_profile() ) :
					bp_get_template_part( 'members/single/profile'  );

				elseif ( bp_is_user_forums() ) :
					bp_get_template_part( 'members/single/forums'   );

				elseif ( bp_is_user_notifications() ) :
					bp_get_template_part( 'members/single/notifications' );

				elseif ( bp_is_user_settings() ) :
					bp_get_template_part( 'members/single/settings' );

				// If nothing sticks, load a generic template
				else :
					//bp_get_template_part( 'members/single/plugins'  );

				endif; ?>

			</div>
		</div><!-- .flexbox-container -->

		<?php
		/**
		 * Fires before the display of member body content.
		 *
		 * @since BuddyPress (1.2.0)
		 */
		do_action( 'bp_after_member_body' ); ?>
	</div><!-- #item-body -->

	<?php

	/**
	 * Fires after the display of member home content.
	 *
	 * @since BuddyPress (1.2.0)
	 */
	do_action( 'bp_after_member_home_content' ); ?>

</div><!-- #buddypress -->
